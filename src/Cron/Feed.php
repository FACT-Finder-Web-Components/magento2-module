<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\FeedServiceInterface;

class Feed
{
    private const PATH_CONFIGURABLE_CRON_IS_ENABLED = 'factfinder/configurable_cron/ff_cron_enabled';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var FeedServiceInterface  */
    private $feedService;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        FeedServiceInterface $feedService
    ) {
        $this->scopeConfig  = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->feedService  = $feedService;
    }

    public function execute(): void
    {
        if (!$this->scopeConfig->isSetFlag(self::PATH_CONFIGURABLE_CRON_IS_ENABLED)) {
            return;
        }

        foreach ($this->storeManager->getStores() as $store) {
            $this->feedService->integrate((int) $store->getId());
        }
    }
}
