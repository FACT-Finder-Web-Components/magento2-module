<?php

namespace Omikron\Factfinder\Test\Unit\Block\FF;

use Omikron\Factfinder\Block\FF\Recommendation;
use \Magento\Framework\View\Element\Template\Context;
use \Magento\Framework\Registry;
use \Magento\Directory\Model\Currency;
use \Magento\Catalog\Helper\Image;
use \Omikron\Factfinder\Helper\Data;

class RecommendationTest extends \PHPUnit_Framework_TestCase
{
    const PRODUCT_IMAGE_PLACEHOLDER = 'product-image-placeholder';
    const CURRENT_CURRENCY_SYMBOL = 'EUR';
    const SHOW_ADD_TO_CART_BUTTON = 1;
    const ADD_TO_CART_URL = 'http://example.com/cart';

    /**
     * @var Omikron\Factfinder\Block\FF\Recommendation
     */
    protected $recommendation;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $currency;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $image;

    /**
     * @var \Omikron\Factfinder\Helper\Data
     */
    protected $data;

    public function setUp()
    {
        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->registry = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->currency = $this->getMockBuilder(Currency::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->currency->method('getCurrencySymbol')
            ->willReturn(self::CURRENT_CURRENCY_SYMBOL);
        $this->image = $this->getMockBuilder(Image::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->image->method('getDefaultPlaceholderUrl')
            ->with($this->equalTo('image'))
            ->willReturn(self::PRODUCT_IMAGE_PLACEHOLDER);
        $this->data = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->data->method('getShowAddToCartButton')
            ->willReturn(self::SHOW_ADD_TO_CART_BUTTON);

        $this->recommendation = new Recommendation($this->context, $this->registry, $this->currency, $this->image, $this->data);
    }

    public function testGetProductImagePlaceholder()
    {
        $this->assertEquals(self::PRODUCT_IMAGE_PLACEHOLDER, $this->recommendation->getProductImagePlaceholder());
    }

    public function testGetAddToCartUrl()
    {
        $recommendation = $this->getMockBuilder(Recommendation::class)
            ->setMethods(array('getUrl'))
            ->disableOriginalConstructor()
            ->getMock();
        $recommendation->method('getUrl')
            ->withAnyParameters()
            ->willReturn(self::ADD_TO_CART_URL);

        $this->assertEquals(self::ADD_TO_CART_URL, $recommendation->getAddToCartUrl());
    }

    public function testIsAddToCartEnabled()
    {
        $this->assertEquals(self::SHOW_ADD_TO_CART_BUTTON, $this->recommendation->isAddToCartEnabled());
    }

    public function testGetCurrentCurrencySymbol()
    {
        $this->assertEquals(self::CURRENT_CURRENCY_SYMBOL, $this->recommendation->getCurrentCurrencySymbol());
    }
}
