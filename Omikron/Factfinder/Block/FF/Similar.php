<?php
/**
 * Created by PhpStorm.
 * User: soroush
 * Date: 13/12/17
 * Time: 15:00
 */

namespace Omikron\Factfinder\Block\FF;

use Magento\Catalog\Model\Product;
use \Magento\Framework\View\Element\Template;

class Similar extends Template
{
    /**
     * @var Product
     */
    protected $_product = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /** @var \Magento\Catalog\Helper\Image */
    protected $_imageHelper;

    /** @var \Omikron\Factfinder\Helper\Data */
    protected $_helper;

    /** @var \Magento\Directory\Model\Currency */
    protected $_currency;

    /**
     * Recommendation constructor.
     * @param Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Omikron\Factfinder\Helper\Data $helper,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        $this->_currency = $currency;
        $this->_imageHelper = $imageHelper;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
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