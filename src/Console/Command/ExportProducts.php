<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Console\Command;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Model\Api\PushImport;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\FtpUploader;
use Omikron\Factfinder\Model\StoreEmulation;
use Omikron\Factfinder\Model\Stream\CsvFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportProducts extends \Symfony\Component\Console\Command\Command
{
    /** @var ScopeConfigInterface */
    private $scopeConfig;

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

    /** @var PushImport */
    private $pushImport;

    /** @var State */
    private $state;

    /** @var Filesystem */
    private $filesystem;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        FeedGeneratorFactory $feedFactory,
        StoreEmulation $emulation,
        CsvFactory $csvFactory,
        FtpUploader $ftpUploader,
        CommunicationConfig $communicationConfig,
        PushImport $pushImport,
        State $state,
        Filesystem $filesystem
    ) {
        parent::__construct();
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->feedGeneratorFactory = $feedFactory;
        $this->storeEmulation = $emulation;
        $this->csvFactory = $csvFactory;
        $this->ftpUploader = $ftpUploader;
        $this->communicationConfig = $communicationConfig;
        $this->pushImport = $pushImport;
        $this->state = $state;
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('factfinder:export:products')->setDescription('Export Factfinder Product Data as CSV file');

        $this->addOption('store', 's', InputOption::VALUE_OPTIONAL, 'Store ID or Store Code');
        $this->addOption('skip-ftp-upload', null, InputOption::VALUE_NONE, 'Skip FTP Upload');
        $this->addOption('skip-push-import', null, InputOption::VALUE_NONE, 'Skip Push Import');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode('frontend');

        if ($storeId = $input->getOption('store')) {
            $storeIds = [$this->storeManager->getStore($storeId)->getId()];
        } else {
            $storeIds = array_map(
                function ($store) {
                    return $store->getId();
                },
                $this->storeManager->getStores()
            );
        }
        foreach ($storeIds as $storeId) {
            $this->storeEmulation->runInStore(
                (int) $storeId,
                function () use ($storeId, $input, $output) {
                    if ($this->communicationConfig->isChannelEnabled((int) $storeId)) {
                        $filename = "export.{$this->communicationConfig->getChannel()}.csv";
                        $stream = $this->csvFactory->create(['filename' => "factfinder/{$filename}"]);
                        $this->feedGeneratorFactory->create('product')->generate($stream);
                        $path = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)
                            ->getAbsolutePath('factfinder' . DIRECTORY_SEPARATOR . $filename);
                        $output->writeln("Store $storeId: File $path has been generated.");
                        if (!$input->getOption('skip-ftp-upload')
                            && $this->scopeConfig->getValue('factfinder/data_transfer/ff_upload_host')) {
                            $this->ftpUploader->upload($filename, $stream);
                            $output->writeln("Store $storeId: File $filename has been uploaded to FTP.");
                        }
                        if (!$input->getOption('skip-push-import')) {
                            if ($this->pushImport->execute((int) $storeId)) {
                                $output->writeln("Store $storeId: Push Import for File $filename has been triggered.");
                            }
                        }
                    }
                }
            );
        }
    }
}
