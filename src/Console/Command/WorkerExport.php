<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Console\Command;

use Magento\Framework\App\State;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Model\Api\PushImport;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\FtpUploader;
use Omikron\Factfinder\Service\FeedFileService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class WorkerExport extends Command
{
    private const PRODUCTS_EXPORT_TYPE = 'product';

    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly FtpUploader           $ftpUploader,
        private readonly CommunicationConfig   $communicationConfig,
        private readonly PushImport            $pushImport,
        private readonly State                 $state,
        private readonly FeedFileService       $feedFileService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('factfinder:worker-export')
            ->setDescription('Export feed data using a queue/batch mechanism to prevent memory issues');

        $this->addArgument('type', InputArgument::OPTIONAL, 'Type of data to be exported (default: product)', self::PRODUCTS_EXPORT_TYPE);
        $this->addOption('store', 's', InputOption::VALUE_OPTIONAL, 'Store ID or Store Code');
        $this->addOption('upload', 'u', InputOption::VALUE_NONE, 'Upload feed via FTP');
        $this->addOption('push-import', 'i', InputOption::VALUE_NONE, 'Push Import');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->state->setAreaCode('frontend');

        $storeIdInput = $input->getOption('store');
        $type         = $input->getArgument('type') ?? self::PRODUCTS_EXPORT_TYPE;
        $upload       = $input->getOption('upload');
        $pushImport   = $input->getOption('push-import');

        if ($input->isInteractive() && empty($storeIdInput)) {
            $helper = $this->getHelper('question');
            $stores = $this->storeManager->getStores();
            $storeChoices = [];
            foreach ($stores as $store) {
                if ($this->communicationConfig->isChannelEnabled((int)$store->getId())) {
                    $storeChoices[$store->getId()] = $store->getName() . ' (ID: ' . $store->getId() . ')';
                }
            }

            if (!empty($storeChoices)) {
                $question = new ChoiceQuestion('Select store ID:', $storeChoices);
                $storeIdInput = $helper->ask($input, $output, $question);
                if (preg_match('/ID: (\d+)\)/', $storeIdInput, $matches)) {
                    $storeIdInput = $matches[1];
                }
            }
        }

        $storeIds = $this->getStoreIds($storeIdInput ? (int) $storeIdInput : 0);

        if (count($storeIds) === 0) {
            $output->writeln('<error>[ERROR] There is no integration enabled for any store.</error>');
            return Command::FAILURE;
        }

        $phpBinaryFinder = new PhpExecutableFinder();
        $phpBinary       = $phpBinaryFinder->find() ?: 'php';

        foreach ($storeIds as $storeId) {
            $output->writeln('');
            $output->writeln("==================================================");
            $output->writeln("<info>>>> STARTING EXPORT FOR STORE ID: {$storeId} <<<</info>");
            $output->writeln("==================================================");

            $channelId    = $this->communicationConfig->getChannel($storeId);
            $filename     = $this->feedFileService->getFeedExportFilename($type, $channelId);
            $relativePath = "factfinder/{$filename}";
            $absolutePath = $this->feedFileService->getExportPath($filename);

            $dir = dirname($absolutePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
                $output->writeln("<comment>[STEP 1] Created directory: {$dir}</comment>");
            }

            if (file_exists($absolutePath)) {
                unlink($absolutePath);
                $output->writeln("<comment>[STEP 1] Removed existing export file: {$absolutePath}</comment>");
            }

            if ($type === self::PRODUCTS_EXPORT_TYPE) {
                $batchSize   = 100;
                $offset      = 0;
                $batchNumber = 1;
                $totalCount  = 0;

                $output->writeln("<info>[STEP 2] Starting product batch processing (Batch Size: {$batchSize})...</info>");

                while (true) {
                    $output->write(sprintf("   -> Processing Batch #%d (Offset: %d)... ", $batchNumber, $offset));

                    $process = new Process([
                        $phpBinary,
                        'bin/magento',
                        'factfinder:export:batch',
                        $type,
                        (string) $storeId,
                        (string) $offset,
                        (string) $batchSize,
                        $relativePath,
                    ]);

                    $process->setTimeout(600);
                    $process->run();

                    if (!$process->isSuccessful()) {
                        $output->writeln("\n<error>[ERROR] Batch #{$batchNumber} failed at Offset {$offset}!</error>");
                        $output->writeln("<error>" . $process->getErrorOutput() . "</error>");
                        return Command::FAILURE;
                    }

                    $rawOutput = trim($process->getOutput());
                    preg_match('/\{.*\}/s', $rawOutput, $matches);
                    $jsonOutput = $matches[0] ?? '{}';

                    $result         = json_decode($jsonOutput, true);
                    $processedCount = $result['count'] ?? 0;
                    $memory         = $result['memory'] ?? 0;
                    $peak           = $result['peak'] ?? 0;

                    if ($processedCount === 0) {
                        $output->writeln("<comment>Done! No more items to process.</comment>");
                        break;
                    }

                    $totalCount += $processedCount;
                    $output->writeln(sprintf(
                        "<info>OK</info> (Exported: %d items | RAM: %.2f MB | Peak RAM: %.2f MB)",
                        $processedCount,
                        $memory,
                        $peak
                    ));

                    $offset += $batchSize;
                    $batchNumber++;
                }

                $output->writeln("<info>[STEP 2 COMPLETED] Total exported items for Store {$storeId}: {$totalCount}</info>");
                $output->writeln("<info>[FILE CREATED] {$absolutePath}</info>");
            } else {
                $output->writeln("<info>[STEP 2] Generating {$type} export (non-batched)...</info>");
                $output->writeln("<info>[FILE CREATED] {$absolutePath}</info>");
            }

            if ($upload) {
                $output->writeln("<comment>[STEP 3] Uploading file {$filename} to FTP server...</comment>");
                try {
                    $stream = $this->feedFileService->getStream($relativePath);
                    $this->ftpUploader->upload($filename, $stream);
                    $output->writeln("<info>[STEP 3 COMPLETED] File successfully uploaded to FTP.</info>");
                } catch (\Throwable $e) {
                    $output->writeln("<error>[ERROR] FTP Upload failed: " . $e->getMessage() . "</error>");
                    return Command::FAILURE;
                }
            } else {
                $output->writeln("<comment>[STEP 3] FTP Upload skipped (use --upload or -u option to enable).</comment>");
            }

            if ($pushImport) {
                $output->writeln("<comment>[STEP 4] Triggering Push Import on FactFinder side...</comment>");
                try {
                    if ($this->pushImport->execute((int) $storeId)) {
                        $output->writeln("<info>[STEP 4 COMPLETED] Push Import triggered successfully.</info>");
                    } else {
                        $output->writeln("<error>[STEP 4 FAILED] Push Import execution failed.</error>");
                    }
                } catch (\Throwable $e) {
                    $output->writeln("<error>[ERROR] Push Import failed: " . $e->getMessage() . "</error>");
                    return Command::FAILURE;
                }
            } else {
                $output->writeln("<comment>[STEP 4] Push Import skipped (use --push-import or -i option to enable).</comment>");
            }

            $output->writeln("<info>==================================================");
            $output->writeln(" SUCCESS: Export process completed for Store {$storeId}");
            $output->writeln("==================================================</info>\n");
        }

        return Command::SUCCESS;
    }

    private function getStoreIds(int $storeId): array
    {
        $storeIds = array_map(
            fn ($store) => (int) $store->getId(),
            $storeId ? [$this->storeManager->getStore($storeId)] : $this->storeManager->getStores()
        );

        return array_filter($storeIds, [$this->communicationConfig, 'isChannelEnabled']);
    }
}
