<?php

namespace Omikron\Factfinder\Test\Unit\Helper;

use \Magento\Framework\App\Helper\Context;
use \Magento\Catalog\Helper\Image;
use \Magento\Eav\Model\Config;
use \Magento\Catalog\Model\ProductRepository;
use \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use \Magento\Catalog\Api\CategoryRepositoryInterface;
use \Magento\Store\Api\Data\StoreInterface;
use \Magento\Catalog\Model\Product;
use Omikron\Factfinder\Helper\Product as ProductHelper;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Catalog\Api\Data\CategoryInterface;
use \Magento\Catalog\Model\Category;

class ProductTest extends \PHPUnit_Framework_TestCase
{
    const STORE_ID = 1;
    const PRODUCT_SKU = 12345;
    const PRODUCT_ID = 12345;
    const PRODUCT_NAME = 'product-name';
    const PRODUCT_DESCRIPTION = 'product-description';
    const PRODUCT_SHORT_DESCRIPTION = 'product-short-description';
    const PRODUCT_URL = 'http://magento.local/product-url';
    const PRODUCT_IMAGE_URL = 'http://magento.local/product-image-url';
    const PRODUCT_PRICE = '10,99';
    const PRODUCT_PRICE_2 = 10;
    const PRODUCT_PRICE_CORRECT_VALUE = 10.00;
    const PRODUCT_MANUFACTURER = 'product-manufacturer';
    const PRODUCT_EAN = 'product-ean';
    const PRODUCT_PARENT_ID_BY_PRODUCT_ID = 123;
    const PRODUCT_AVAILABILITY = 1;
    const PRODUCT_CATEGORY_IDS = [12345, 23456, 34567];

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $image;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
     */
    protected $configurable;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepositoryInterface;

    /**
     * @var Magento\Store\Model\Store
     */
    protected $store;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var Omikron\Factfinder\Helper\Product
     */
    protected $productHelper;

    public function setUp()
    {
        $dataMap = [
            ['sku', null, self::PRODUCT_SKU],
            ['name', null, self::PRODUCT_NAME],
            ['description', null, self::PRODUCT_DESCRIPTION],
            ['short_description', null, self::PRODUCT_SHORT_DESCRIPTION],
            ['price', null, self::PRODUCT_PRICE],
            [self::PRODUCT_MANUFACTURER, null, self::PRODUCT_MANUFACTURER],
            [self::PRODUCT_EAN, null, self::PRODUCT_EAN]
        ];

        $scopeConfigMap = [
            [ProductHelper::PATH_DATA_TRANSFER_MANUFACTURER, 'store', self::STORE_ID, self::PRODUCT_MANUFACTURER],
            [ProductHelper::PATH_DATA_TRANSFER_EAN, 'store', self::STORE_ID, self::PRODUCT_EAN]
        ];

        $categoryInterface = $this->getMockBuilder(CategoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $categoryInterface->method('getParentId')
            ->willReturn(Category::ROOT_CATEGORY_ID);

        $categoryRepositoryInterfaceMap = [
            [self::PRODUCT_CATEGORY_IDS[0], null, $categoryInterface],
            [self::PRODUCT_CATEGORY_IDS[1], null, $categoryInterface],
            [self::PRODUCT_CATEGORY_IDS[2], null, $categoryInterface]
        ];

        $scopeConfig = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $scopeConfig->method('getValue')
            ->will($this->returnValueMap($scopeConfigMap));

        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->context->method('getScopeConfig')
            ->willReturn($scopeConfig);

        $this->image = $this->getMockBuilder(Image::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->image->method('init')
            ->willReturn($this->image);
        $this->image->method('constrainOnly')
            ->willReturn($this->image);
        $this->image->method('keepAspectRatio')
            ->willReturn($this->image);
        $this->image->method('keepTransparency')
            ->willReturn($this->image);
        $this->image->method('resize')
            ->willReturn($this->image);
        $this->image->method('getUrl')
            ->willReturn(self::PRODUCT_IMAGE_URL);

        $this->config = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->configurable = $this->getMockBuilder(Configurable::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->configurable->method('getParentIdsByChild')
            ->with($this->equalTo(self::PRODUCT_ID))
            ->willReturn(self::PRODUCT_PARENT_ID_BY_PRODUCT_ID);

        $this->categoryRepositoryInterface = $this->getMockBuilder(CategoryRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->categoryRepositoryInterface->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($categoryRepositoryInterfaceMap));

        $this->store = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->store->method('getId')
            ->willReturn(self::STORE_ID);

        $this->product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->product->method('getData')
            ->will($this->returnValueMap($dataMap));
        $this->product->method('getUrlInStore')
            ->willReturn(self::PRODUCT_URL);
        $this->product->method('isAvailable')
            ->willReturn(self::PRODUCT_AVAILABILITY);
        $this->product->method('getId')
            ->willReturn(self::PRODUCT_ID);
        $this->product->method('getSku')
            ->willReturn(self::PRODUCT_SKU);
        $this->product->method('getCategoryIds')
            ->willReturn(self::PRODUCT_CATEGORY_IDS);

        $this->productRepository = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->productRepository->method('getById')
            ->with($this->equalTo(self::PRODUCT_PARENT_ID_BY_PRODUCT_ID))
            ->willReturn($this->product);

        $this->productHelper = new ProductHelper($this->context, $this->image, $this->config, $this->productRepository, $this->configurable, $this->categoryRepositoryInterface);
    }

    public function testGetUnknownMethod()
    {
        $this->assertNull($this->productHelper->get('unknown-attribute-name', $this->product, $this->store));
    }

    public function testGetProductNumber()
    {
        $this->assertEquals(self::PRODUCT_SKU, $this->productHelper->get('ProductNumber', $this->product, $this->store));
    }

    public function testGetMasterProductNumber()
    {
        $this->assertEquals(self::PRODUCT_SKU, $this->productHelper->get('MasterProductNumber', $this->product, $this->store));
    }

    public function testGetMasterProductNumberWhenParentByChildIsArray()
    {
        $this->configurable = $this->getMockBuilder(Configurable::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->configurable->method('getParentIdsByChild')
            ->with($this->equalTo(self::PRODUCT_ID))
            ->willReturn(array(self::PRODUCT_PARENT_ID_BY_PRODUCT_ID));

        $this->productHelper = new ProductHelper($this->context, $this->image, $this->config, $this->productRepository, $this->configurable, $this->categoryRepositoryInterface);

        $this->assertEquals(self::PRODUCT_SKU, $this->productHelper->get('MasterProductNumber', $this->product, $this->store));
    }

    public function testGetName()
    {
        $this->assertEquals(self::PRODUCT_NAME, $this->productHelper->get('Name', $this->product, $this->store));
    }

    public function testGetDescription()
    {
        $this->assertEquals(self::PRODUCT_DESCRIPTION, $this->productHelper->get('Description', $this->product, $this->store));
    }

    public function testGetShort()
    {
        $this->assertEquals(self::PRODUCT_SHORT_DESCRIPTION, $this->productHelper->get('Short', $this->product, $this->store));
    }

    public function testGetProductUrl()
    {
        $this->assertEquals(self::PRODUCT_URL, $this->productHelper->get('ProductUrl', $this->product, $this->store));
    }

    public function testGetImageUrl()
    {
        $this->assertEquals(self::PRODUCT_IMAGE_URL, $this->productHelper->get('ImageUrl', $this->product, $this->store));
    }

    public function testGetPrice()
    {
        $this->assertEquals(self::PRODUCT_PRICE_CORRECT_VALUE, $this->productHelper->get('Price', $this->product, $this->store));
    }

    public function testGetPriceWithInvalidFormat()
    {
        $this->product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->product->method('getData')
            ->with($this->equalTo('price'))
            ->willReturn(self::PRODUCT_PRICE_2);

        $this->assertEquals(self::PRODUCT_PRICE_CORRECT_VALUE, $this->productHelper->get('Price', $this->product, $this->store));
    }

    public function testGetCategoryPathWhenEmpty()
    {
        $this->assertEquals('', $this->productHelper->get('CategoryPath', $this->product, $this->store));
    }

    public function testGetAvailability()
    {
        $this->assertEquals(self::PRODUCT_AVAILABILITY, $this->productHelper->get('Availability', $this->product, $this->store));
    }

    public function testGetMagentoEntityId()
    {
        $this->assertEquals(self::PRODUCT_ID, $this->productHelper->get('MagentoEntityId', $this->product, $this->store));
    }

    public function testGetManufacturer()
    {
        $this->assertEquals(self::PRODUCT_MANUFACTURER, $this->productHelper->get('Manufacturer', $this->product, $this->store));
    }

    public function testGetEAN()
    {
        $this->assertEquals(self::PRODUCT_EAN, $this->productHelper->get('EAN', $this->product, $this->store));
    }
}
