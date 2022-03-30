<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Console\Command;

use Magento\Framework\App\State;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Model\Api\PushImport;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\FtpUploader;
use Omikron\Factfinder\Model\StoreEmulation;
use Omikron\Factfinder\Api\StreamInterfaceFactory;
use Omikron\Factfinder\Service\FeedFileService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Export extends Command
{
    private StoreEmulation $storeEmulation;
    private FeedGeneratorFactory $feedGeneratorFactory;
    private StoreManagerInterface $storeManager;
    private CommunicationConfig $communicationConfig;
    private StreamInterfaceFactory $streamFactory;
    private FtpUploader $ftpUploader;
    private PushImport $pushImport;
    private State $state;
    private FeedFileService $feedFileService;

    public function __construct(
        StoreManagerInterface $storeManager,
        FeedGeneratorFactory $feedFactory,
        StoreEmulation $emulation,
        StreamInterfaceFactory $streamFactory,
        FtpUploader $ftpUploader,
        CommunicationConfig $communicationConfig,
        PushImport $pushImport,
        State $state,
        FeedFileService $feedFileService
    ) {
        parent::__construct();
        $this->storeManager         = $storeManager;
        $this->feedGeneratorFactory = $feedFactory;
        $this->storeEmulation       = $emulation;
        $this->streamFactory        = $streamFactory;
        $this->ftpUploader          = $ftpUploader;
        $this->communicationConfig  = $communicationConfig;
        $this->pushImport           = $pushImport;
        $this->state                = $state;
        $this->feedFileService      = $feedFileService;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('factfinder:export')->setDescription('Export feed data as CSV file');

        $this->addArgument('type', InputArgument::REQUIRED, 'type of data to be exported. Possible values are : product, cms');
        $this->addOption('store', 's', InputOption::VALUE_OPTIONAL, 'Store ID or Store Code');
        $this->addOption('upload', 'u', InputOption::VALUE_NONE, 'Upload feed via FTP');
        $this->addOption('push-import', 'i', InputOption::VALUE_NONE, 'Push Import');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode('frontend');
        $storeIds = $this->getStoreIds((int) $input->getOption('store'));
        $type     = $input->getArgument('type');

        if (count($storeIds) === 0) {
            $output->writeln('There is no integration enabled for any store');
            return 0;
        }

        foreach ($storeIds as $storeId) {
            $this->storeEmulation->runInStore($storeId, function () use ($storeId, $input, $output, $type) {
                $filename        = $this->feedFileService->getFeedExportFilename($type, $this->communicationConfig->getChannel($storeId));
                $stream          = $this->streamFactory->create(['filename' => "factfinder/{$filename}"]);
                $path            = $this->feedFileService->getExportPath($filename);

                $this->feedGeneratorFactory->create($type)->generate($stream);
                $output->writeln("Store {$storeId}: File {$path} has been generated.");

                if ($input->getOption('upload')) {
                    $this->ftpUploader->upload($filename, $stream);
                    $output->writeln("Store {$storeId}: File {$filename} has been uploaded to FTP.");
                }

                if ($input->getOption('push-import') && $this->pushImport->execute((int) $storeId)) {
                    $output->writeln("Store {$storeId}: Push Import for File {$filename} has been triggered.");
                }
            });
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
