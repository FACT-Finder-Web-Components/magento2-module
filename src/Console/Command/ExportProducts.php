<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Console\Command;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\FactFinder\Communication\Resource\Builder;
use Omikron\Factfinder\Model\Api\CredentialsFactory;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\Config\ExportConfig;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\FtpUploader;
use Omikron\Factfinder\Model\StoreEmulation;
use Omikron\Factfinder\Model\Stream\CsvFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportProducts extends Command
{
    /** @var StoreEmulation */
    private $storeEmulation;

    /** @var FeedGeneratorFactory */
    private $feedGeneratorFactory;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var CommunicationConfig */
    private $communicationConfig;

    /** @var CsvFactory */
    private $csvFactory;

    /** @var FtpUploader */
    private $ftpUploader;

    /** @var CredentialsFactory */
    private $credentialsFactory;

    /** @var ExportConfig */
    private $exportConfig;

    /** @var State */
    private $state;

    /** @var Filesystem */
    private $filesystem;

    public function __construct(
        StoreManagerInterface $storeManager,
        FeedGeneratorFactory $feedFactory,
        StoreEmulation $emulation,
        CsvFactory $csvFactory,
        FtpUploader $ftpUploader,
        CommunicationConfig $communicationConfig,
        CredentialsFactory $credentialsFactory,
        ExportConfig $exportConfig,
        State $state,
        Filesystem $filesystem
    ) {
        parent::__construct();
        $this->storeManager         = $storeManager;
        $this->feedGeneratorFactory = $feedFactory;
        $this->storeEmulation       = $emulation;
        $this->csvFactory           = $csvFactory;
        $this->ftpUploader          = $ftpUploader;
        $this->communicationConfig  = $communicationConfig;
        $this->credentialsFactory   = $credentialsFactory;
        $this->exportConfig         = $exportConfig;
        $this->state                = $state;
        $this->filesystem           = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('factfinder:export:products')->setDescription('Export Factfinder Product Data as CSV file');

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

        foreach ($this->getStoreIds((int)$input->getOption('store')) as $storeId) {
            $this->storeEmulation->runInStore($storeId, function () use ($storeId, $input, $output) {
                $filename = "export.{$this->communicationConfig->getChannel($storeId)}.csv";
                $stream   = $this->csvFactory->create(['filename' => "factfinder/{$filename}"]);
                $this->feedGeneratorFactory->create('product')->generate($stream);
                $path = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)
                    ->getAbsolutePath('factfinder' . DIRECTORY_SEPARATOR . $filename);
                $output->writeln("Store {$storeId}: File {$path} has been generated.");

                if ($input->getOption('upload')) {
                    $this->ftpUploader->upload($filename, $stream);
                    $output->writeln("Store {$storeId}: File {$filename} has been uploaded to FTP.");
                }

                if ($input->getOption('push-import')) {
                    $pushImport = (new Builder())
                        ->withApiVersion($this->communicationConfig->getVersion())
                        ->withServerUrl($this->communicationConfig->getAddress())
                        ->withCredentials($this->credentialsFactory->create())
                        ->build();

                    foreach ($this->exportConfig->getPushImportDataTypes($storeId) as $dataType) {
                        $pushImport->import($dataType, $this->communicationConfig->getChannel($storeId));
                    }
                    $output->writeln("Store {$storeId}: Push Import for File {$filename} has been triggered.");
                }
            });
        }
    }

    private function getStoreIds(int $storeId): array
    {
        $storeIds = array_map(function ($store) {
            return (int) $store->getId();
        }, $storeId ? [$this->storeManager->getStore($storeId)] : $this->storeManager->getStores());
        return array_filter($storeIds, [$this->communicationConfig, 'isChannelEnabled']);
    }
}
