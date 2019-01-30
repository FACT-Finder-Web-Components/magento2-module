<?php

namespace Omikron\Factfinder\Block\FF;

use Magento\Framework\View\Element\Template;

/**
 * Block Class FF Suggest
 * @package Omikron\Factfinder\Block\FF
 */
class Suggest extends Template
{
    /** @var \Omikron\Factfinder\Helper\Data */
    protected $_helper;

    /**
     * Suggest constructor.
     * @param Template\Context $context
     * @param \Omikron\Factfinder\Helper\Data $helper
     * @param array $data
     */
    public function __construct(Template\Context $context, \Omikron\Factfinder\Helper\Data $helper, array $data = [])
    {
        parent::__construct($context, $data);
        $this->_helper = $helper;
    }

    /**
     * Overwrite toHtml() function to check if suggest component is activated
     * @return string
     */
    public function toHtml()
    {
        if (!$this->_helper->getFFSuggest()) {
            return '';
        }
        return parent::toHtml();
    }
}
