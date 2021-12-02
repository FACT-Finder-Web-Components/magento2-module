<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\Config\ChannelProviderInterface;
use Omikron\Factfinder\Model\Api\PushImport;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\FtpUploader;
use Omikron\Factfinder\Model\StoreEmulation;
use Omikron\Factfinder\Model\Stream\CsvFactory;
use Omikron\Factfinder\Service\FeedFileService;

class Feed
{
    private const PATH_CONFIGURABLE_CRON_IS_ENABLED = 'factfinder/configurable_cron/ff_cron_enabled';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var StoreEmulation */
    private $storeEmulation;

    /** @var FeedGeneratorFactory */
    private $feedGeneratorFactory;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var ChannelProviderInterface */
    private $channelProvider;

    /** @var CsvFactory */
    private $csvFactory;

    /** @var FtpUploader */
    private $ftpUploader;

    /** @var string */
    private $feedType;

    /** @var PushImport */
    private $pushImport;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        FeedGeneratorFactory $feedFactory,
        StoreEmulation $emulation,
        CsvFactory $csvFactory,
        FtpUploader $ftpUploader,
        ChannelProviderInterface $channelProvider,
        PushImport $pushImport,
        string $type
    ) {
        $this->scopeConfig          = $scopeConfig;
        $this->storeManager         = $storeManager;
        $this->feedGeneratorFactory = $feedFactory;
        $this->storeEmulation       = $emulation;
        $this->csvFactory           = $csvFactory;
        $this->ftpUploader          = $ftpUploader;
        $this->channelProvider      = $channelProvider;
        $this->pushImport           = $pushImport;
        $this->feedType             = $type;
    }

    public function execute(): void
    {
        if (!$this->scopeConfig->isSetFlag(self::PATH_CONFIGURABLE_CRON_IS_ENABLED)) {
            return;
        }

        foreach ($this->storeManager->getStores() as $store) {
            $this->storeEmulation->runInStore((int) $store->getId(), function () use ($store) {
                if ($this->channelProvider->isChannelEnabled((int) $store->getId())) {
                    $filename = (new FeedFileService())->getFeedExportFilename($this->feedType, $this->channelProvider->getChannel((int) $store->getId()));
                    $stream   = $this->csvFactory->create(['filename' => "factfinder/{$filename}"]);
                    $this->feedGeneratorFactory->create($this->feedType)->generate($stream);
                    $this->ftpUploader->upload($filename, $stream);
                    $this->pushImport->execute((int) $store->getId());
                }
            });
        }
    }
}
