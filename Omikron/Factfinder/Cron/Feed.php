<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Omikron\Factfinder\Model\Export\Product;

class Feed
{
    private const PATH_CONFIGURABLE_CRON_IS_ENABLED = 'factfinder/configurable_cron/ff_cron_enabled';

    /** @var Product */
    private $productExport;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(Product $productExport, ScopeConfigInterface $scopeConfig)
    {
        $this->productExport = $productExport;
        $this->scopeConfig   = $scopeConfig;
    }

    public function execute()
    {
        if ($this->scopeConfig->isSetFlag(self::PATH_CONFIGURABLE_CRON_IS_ENABLED)) {
            $this->productExport->exportProducts(true);
        }
    }
}
