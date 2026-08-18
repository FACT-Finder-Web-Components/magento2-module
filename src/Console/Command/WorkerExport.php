<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Console\Command;

use Magento\Framework\App\State;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\StreamInterfaceFactory;
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
        private readonly StoreManagerInterface  $storeManager,
        private readonly FtpUploader            $ftpUploader,
        private readonly CommunicationConfig    $communicationConfig,
        private readonly PushImport             $pushImport,
        private readonly State                  $state,
        private readonly FeedFileService        $feedFileService,
        private readonly StreamInterfaceFactory $streamFactory
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('factfinder:worker-export')
            ->setDescription('Export feed data using a queue/batch mechanism to prevent memory issues');

        $this->addArgument(
            'type',
            InputArgument::OPTIONAL,
            'Type of data to be exported (default: product)',
            self::PRODUCTS_EXPORT_TYPE
        );
        $this->addOption('store', 's', InputOption::VALUE_OPTIONAL, 'Store ID or Store Code');
        $this->addOption('upload', 'u', InputOption::VALUE_NONE, 'Upload feed via FTP');
        $this->addOption('push-import', 'i', InputOption::VALUE_NONE, 'Push Import');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->state->setAreaCode('frontend');

        [$storeIds, $upload, $pushImport] = $this->resolveExecutionParameters($input, $output);

        if (empty($storeIds)) {
            $output->writeln('<error>[ERROR] There is no integration enabled for any store.</error>');
            return Command::FAILURE;
        }

        $phpBinaryFinder = new PhpExecutableFinder();
        $phpBinary       = $phpBinaryFinder->find() ?: 'php';
        $type            = $input->getArgument('type') ?? self::PRODUCTS_EXPORT_TYPE;

        foreach ($storeIds as $storeId) {
            $success = $this->exportForStore($storeId, $type, $upload, $pushImport, $output, $phpBinary);
            if (!$success) {
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }

    private function resolveExecutionParameters(InputInterface $input, OutputInterface $output): array
    {
        $storeIdInput = $input->getOption('store');
        $upload       = (bool) $input->getOption('upload');
        $pushImport   = (bool) $input->getOption('push-import');

        if ($input->isInteractive()) {
            $helper = $this->getHelper('question');

            if (empty($storeIdInput)) {
                $storeIdInput = $this->askStoreId($input, $output, $helper);
            }

            if (!$upload) {
                $upload = $this->askYesNoQuestion(
                    $input,
                    $output,
                    $helper,
                    'Should upload feed to FTP after exporting?'
                );
            }

            if (!$pushImport) {
                $pushImport = $this->askYesNoQuestion(
                    $input,
                    $output,
                    $helper,
                    'Should trigger Push Import after uploading?'
                );
            }
        }

        $storeIds = $this->getStoreIds($storeIdInput ? (int) $storeIdInput : 0);

        return [$storeIds, $upload, $pushImport];
    }

    private function askStoreId(InputInterface $input, OutputInterface $output, mixed $helper): ?string
    {
        $storeChoices = [];
        foreach ($this->storeManager->getStores() as $store) {
            if ($this->communicationConfig->isChannelEnabled((int) $store->getId())) {
                $storeChoices[$store->getId()] = "{$store->getName()} (ID: {$store->getId()})";
            }
        }

        if (empty($storeChoices)) {
            return null;
        }

        $question     = new ChoiceQuestion('Select store ID:', $storeChoices);
        $storeIdInput = $helper->ask($input, $output, $question);

        if (preg_match('/ID: (\d+)\)/', (string) $storeIdInput, $matches)) {
            return $matches[1];
        }

        return (string) $storeIdInput;
    }

    private function askYesNoQuestion(
        InputInterface $input,
        OutputInterface $output,
        mixed $helper,
        string $questionText
    ): bool {
        $question = new ChoiceQuestion("{$questionText} (default: no)", ['no', 'yes'], 0);
        $answer   = $helper->ask($input, $output, $question);

        return $answer === 'yes';
    }

    private function exportForStore(
        int $storeId,
        string $type,
        bool $upload,
        bool $pushImport,
        OutputInterface $output,
        string $phpBinary
    ): bool {
        $output->writeln('');
        $output->writeln('==================================================');
        $output->writeln("<info>>>> STARTING EXPORT FOR STORE ID: {$storeId} <<<</info>");
        $output->writeln('==================================================');

        $channelId    = $this->communicationConfig->getChannel($storeId);
        $filename     = $this->feedFileService->getFeedExportFilename($type, $channelId);
        $relativePath = "factfinder/{$filename}";
        $absolutePath = $this->feedFileService->getExportPath($filename);

        $this->prepareExportFile($absolutePath, $output);

        if ($type === self::PRODUCTS_EXPORT_TYPE) {
            $batchSuccess = $this->runProductBatchExport($storeId, $type, $relativePath, $output, $phpBinary);
            if (!$batchSuccess) {
                return false;
            }
            $output->writeln("<info>[FILE CREATED] {$absolutePath}</info>");
        }

        if (!$this->handleUpload($upload, $filename, $relativePath, $output)) {
            return false;
        }

        if (!$this->handlePushImport($pushImport, $storeId, $output)) {
            return false;
        }

        $output->writeln("<info> SUCCESS: Export process completed for Store {$storeId}</info>\n");
        return true;
    }

    private function prepareExportFile(string $absolutePath, OutputInterface $output): void
    {
        $dir = dirname($absolutePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            $output->writeln("<comment>[STEP 1] Created directory: {$dir}</comment>");
        }

        if (file_exists($absolutePath)) {
            unlink($absolutePath);
            $output->writeln("<comment>[STEP 1] Removed existing export file: {$absolutePath}</comment>");
        }
    }

    private function runProductBatchExport(
        int $storeId,
        string $type,
        string $relativePath,
        OutputInterface $output,
        string $phpBinary
    ): bool {
        $batchSize   = 100;
        $offset      = 0;
        $batchNumber = 1;
        $totalCount  = 0;

        $output->writeln("<info>[STEP 2] Starting product batch processing (Batch Size: {$batchSize})...</info>");

        while (true) {
            $output->write(sprintf('   -> Processing Batch #%d (Offset: %d)... ', $batchNumber, $offset));

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
                $output->writeln("<error>{$process->getErrorOutput()}</error>");
                return false;
            }

            $result = $this->parseProcessOutput($process->getOutput());
            $count  = $result['count'] ?? 0;

            if ($count === 0) {
                $output->writeln('<comment>Done! No more items to process.</comment>');
                break;
            }

            $totalCount += $count;
            $output->writeln(sprintf(
                '<info>OK</info> (Exported: %d items | RAM: %.2f MB | Peak RAM: %.2f MB)',
                $count,
                $result['memory'] ?? 0,
                $result['peak'] ?? 0
            ));

            $offset += $batchSize;
            $batchNumber++;
        }

        $output->writeln("<info>[STEP 2 COMPLETED] Total exported items: {$totalCount}</info>");
        return true;
    }

    private function parseProcessOutput(string $rawOutput): array
    {
        preg_match('/\{.*\}/s', trim($rawOutput), $matches);
        return json_decode($matches[0] ?? '{}', true) ?: [];
    }

    private function handleUpload(
        bool $upload,
        string $filename,
        string $relativePath,
        OutputInterface $output
    ): bool {
        if (!$upload) {
            $output->writeln('<comment>[STEP 3] FTP Upload skipped.</comment>');
            return true;
        }

        $output->writeln("<comment>[STEP 3] Uploading file {$filename} to FTP server...</comment>");
        try {
            $stream = $this->streamFactory->create([
                'filename' => $relativePath,
                'mode'     => 'r',
            ]);
            $this->ftpUploader->upload($filename, $stream);
            $output->writeln('<info>[STEP 3 COMPLETED] File successfully uploaded to FTP.</info>');
            return true;
        } catch (\Throwable $e) {
            $output->writeln("<error>[ERROR] FTP Upload failed: {$e->getMessage()}</error>");
            return false;
        }
    }

    private function handlePushImport(
        bool $pushImport,
        int $storeId,
        OutputInterface $output
    ): bool {
        if (!$pushImport) {
            $output->writeln('<comment>[STEP 4] Push Import skipped.</comment>');
            return true;
        }

        $output->writeln('<comment>[STEP 4] Triggering Push Import on FactFinder side...</comment>');
        try {
            if ($this->pushImport->execute($storeId)) {
                $output->writeln('<info>[STEP 4 COMPLETED] Push Import triggered successfully.</info>');
                return true;
            }
            $output->writeln('<error>[STEP 4 FAILED] Push Import execution failed.</error>');
            return false;
        } catch (\Throwable $e) {
            $output->writeln("<error>[ERROR] Push Import failed: {$e->getMessage()}</error>");
            return false;
        }
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
