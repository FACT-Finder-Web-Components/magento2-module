<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Cron;

use Magento\Api\Data\StoreInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\Config\ChannelProviderInterface;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\StoreEmulation;
use Omikron\Factfinder\Model\Stream\FtpFactory;

class Feed
{
    private const PATH_CONFIGURABLE_CRON_IS_ENABLED = 'factfinder/configurable_cron/ff_cron_enabled';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var StoreEmulation */
    private $storeEmulation;

    /** @var FeedGeneratorFactory */
    private $feedGeneratorFactory;

    /** @var FtpFactory */
    private $ftpFactory;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var ChannelProviderInterface */
    private $channelProvider;

    /** @var string */
    private $feedType;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        FeedGeneratorFactory $feedFactory,
        StoreEmulation $emulation,
        FtpFactory $ftpFactory,
        ChannelProviderInterface $channelProvider,
        string $type
    ) {
        $this->scopeConfig          = $scopeConfig;
        $this->storeManager         = $storeManager;
        $this->feedGeneratorFactory = $feedFactory;
        $this->storeEmulation       = $emulation;
        $this->ftpFactory           = $ftpFactory;
        $this->channelProvider      = $channelProvider;
        $this->feedType             = $type;
    }

    public function execute(): void
    {
        if ($this->scopeConfig->isSetFlag(self::PATH_CONFIGURABLE_CRON_IS_ENABLED)) {
            /** @var StoreInterface $store */
            foreach ($this->storeManager->getStores() as $store) {
                $this->storeEmulation->runInStore((int) $store->getId(), function () use ($store) {
                    if ($this->channelProvider->isChannelEnabled((int) $store->getId())) {
                        $channel       = $this->channelProvider->getChannel();
                        $filename      = "factfinder/export.{$channel}.csv";
                        $feedGenerator = $this->feedGeneratorFactory->create($this->feedType);
                        $feedGenerator->generate($this->ftpFactory->create(['filename' => $filename]));
                    }
                });
            }
        }
    }
}
