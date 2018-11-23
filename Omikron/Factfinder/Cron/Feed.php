<?php

namespace Omikron\Factfinder\Cron;

class Feed
{
    /** @var \Omikron\Factfinder\Model\Export\Product */
    protected $productExport;

    /** @var \Omikron\Factfinder\Helper\Data */
    protected $configHelper;

    /**
     * Feed constructor.
     *
     * @param \Omikron\Factfinder\Model\Export\Product $productExport
     * @param \Omikron\Factfinder\Helper\Data          $configHelper
     */
    public function __construct(
        \Omikron\Factfinder\Model\Export\Product $productExport,
        \Omikron\Factfinder\Helper\Data $configHelper
    ) {
        $this->productExport = $productExport;
        $this->configHelper  = $configHelper;
    }

    /**
     * Executed on Cron Run
     */
    public function execute()
    {
        if ($this->configHelper->isCronEnabled()) {
            $this->productExport->exportProducts(true);
        }
    }
}
