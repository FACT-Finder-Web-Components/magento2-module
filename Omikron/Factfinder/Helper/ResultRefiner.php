<?php

namespace Omikron\Factfinder\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class ResultRefiner
 * @package Omikron\Factfinder\Helper
 */
class ResultRefiner extends AbstractHelper
{
    /**
     * ResultRefiner constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
        /* Add new dependencies here, if needed for JSON enrichment */
    )
    {
        parent::__construct($context);
    }

    /**
     * Refine / enrich the json Result
     *
     * @param string $jsonString
     * @return string $jsonString
     */
    public function refine($jsonString)
    {
        /* enrich JSON data here */

        return $jsonString;
    }
}
