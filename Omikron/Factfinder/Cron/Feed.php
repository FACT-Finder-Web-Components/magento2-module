<?php

namespace Omikron\Factfinder\Cron;

class Feed
{
    /** @var \Omikron\Factfinder\Model\Export\Product */
    protected $productExport;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $scopeConfig;

    /**
     * Feed constructor.
     *
     * @param \Omikron\Factfinder\Model\Export\Product $productExport
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Omikron\Factfinder\Model\Export\Product $productExport,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->productExport = $productExport;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Executed on Cron Run
     */
    public function execute()
    {
        if ($this->scopeConfig->getValue('factfinder/data_transfer/ff_cron_enabled')) {
            $this->productExport->exportProducts(true);
        }
    }
}