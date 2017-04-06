<?php

namespace Omikron\Factfinder\Block\FF;

use \Magento\Framework\View\Element\Template;

/**
 * Block Class FF RecordList
 * @package Omikron\Factfinder\Block\FF
 */
class RecordList extends Template
{
    /** @var \Magento\Catalog\Helper\Image */
    protected $_imageHelper;

    /** @var \Omikron\Factfinder\Helper\Data */
    protected $_helper;

    /** @var \Magento\Directory\Model\Currency */
    protected $_currency;

    /**
     * RecordList constructor.
     *
     * @param Template\Context $context
     * @param \Magento\Directory\Model\Currency $currency
     * @param array $data
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Omikron\Factfinder\Helper\Data $helper
     */
    public function __construct(
        Template\Context $context,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Omikron\Factfinder\Helper\Data $helper,
        $data = []
    )
    {
        $this->_currency = $currency;
        $this->_imageHelper = $imageHelper;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Get the placeholder image url for products
     *
     * @return string
     */
    public function getProductImagePlaceholder()
    {
        return $this->_imageHelper->getDefaultPlaceholderUrl('image');
    }

    /**
     * Get the url for submitting the addToCart form
     *
     * @return string
     */
    public function getAddToCartUrl()
    {
        return $this->getUrl('checkout/cart/add');
    }

    /**
     * Test if the addToCart is enabled in the factfinder options
     *
     * @return mixed
     */
    public function isAddToCartEnabled()
    {
        return $this->_helper->getShowAddToCartButton();
    }

    /**
     * Get current store currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {

        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Get currency symbol for current locale and currency code
     *
     * @return string
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }

}