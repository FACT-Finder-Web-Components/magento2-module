<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\FactFinder\Communication\Resource\Builder;
use Omikron\Factfinder\Model\Api\PushImport;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\FtpUploader;
use Omikron\Factfinder\Model\StoreEmulation;
use Omikron\Factfinder\Api\StreamInterfaceFactory;
use Omikron\Factfinder\Service\FeedFileService;

class Feed
{
    private const PATH_CONFIGURABLE_CRON_IS_ENABLED = 'factfinder/configurable_cron/ff_cron_enabled';

    private ScopeConfigInterface $scopeConfig;
    private StoreEmulation $storeEmulation;
    private FeedGeneratorFactory $feedGeneratorFactory;
    private StoreManagerInterface $storeManager;
    private CommunicationConfig $communicationConfig;
    private StreamInterfaceFactory $streamFactory;
    private FtpUploader $ftpUploader;
    private PushImport $pushImport;
    private FeedFileService $feedFileService;
    private string $feedType;

    /**
     * @param ScopeConfigInterface   $scopeConfig
     * @param StoreManagerInterface  $storeManager
     * @param FeedGeneratorFactory   $feedFactory
     * @param StoreEmulation         $emulation
     * @param StreamInterfaceFactory $streamFactory
     * @param FtpUploader            $ftpUploader
     * @param CommunicationConfig    $communicationConfig
     * @param PushImport             $pushImport
     * @param FeedFileService        $feedFileService
     * @param string                 $type
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        FeedGeneratorFactory $feedFactory,
        StoreEmulation $emulation,
        StreamInterfaceFactory $streamFactory,
        FtpUploader $ftpUploader,
        CommunicationConfig $communicationConfig,
        PushImport $pushImport,
        FeedFileService $feedFileService,
        string $type
    ) {
        $this->scopeConfig          = $scopeConfig;
        $this->storeManager         = $storeManager;
        $this->feedGeneratorFactory = $feedFactory;
        $this->storeEmulation       = $emulation;
        $this->streamFactory       = $streamFactory;
        $this->ftpUploader          = $ftpUploader;
        $this->communicationConfig  = $communicationConfig;
        $this->pushImport           = $pushImport;
        $this->feedFileService      = $feedFileService;
        $this->feedType             = $type;
    }

    public function execute(): void
    {
        if (!$this->scopeConfig->isSetFlag(self::PATH_CONFIGURABLE_CRON_IS_ENABLED)) {
            return;
        }

        foreach ($this->storeManager->getStores() as $store) {
            $this->storeEmulation->runInStore((int) $store->getId(), function () use ($store) {
                $storeId = (int) $store->getId();
                if ($this->communicationConfig->isChannelEnabled($storeId)) {
                    $filename = (new FeedFileService())->getFeedExportFilename($this->feedType, $this->communicationConfig->getChannel());
                    $stream   = $this->streamFactory->create(['filename' => "factfinder/{$filename}"]);
                    $this->feedGeneratorFactory->create($this->feedType)->generate($stream);
                    $this->ftpUploader->upload($filename, $stream);
                    if ($this->communicationConfig->isPushImportEnabled($storeId)) {
                        $this->pushImport->execute($storeId);
                    }
                }
            });
        }
    }
}
