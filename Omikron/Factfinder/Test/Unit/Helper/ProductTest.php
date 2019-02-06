<?php

namespace Omikron\Factfinder\Test\Unit\Helper;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Catalog\Model\Entity\Attribute;
use Magento\Catalog\Model\ProductRepository;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Api\Data\StoreInterface;
use Omikron\Factfinder\Helper\Product;

class ProductTest extends \PHPUnit\Framework\TestCase
{
    const STORE_ID                         = 1;
    const TEST_ATTRIBUTE_CODE              = 'test';
    const VARCHAR_TEST_PRODUCT_VALUE       = 'test value';
    const SELECT_TEST_PRODUCT_VALUE        = 'option label';
    const BOOLEAN_TEST_PRODUCT_VALUE_LABEL = 'Yes';
    const BOOLEAN_TEST_PRODUCT_VALUE       = '1';
    const MULTI_SELECT_TEST_PRODUCT_VALUE  = 'option label1, option label2';
    const SELECT_TEST_OPTION_ID            = 1;
    const MULTI_SELECT_TEST_OPTION_ID      = '1,2';
    const HTML_TAGS_ATTRIBUTE_VALUE        = '&lt;div class=&quot;test description&quot;&gt; This is test attribute value &lt;/div&gt;';

    /** @var \Magento\Framework\App\Helper\Context | \PHPUnit_Framework_MockObject_MockObject */
    protected $contextMock;

    /**  @var \Magento\Catalog\Helper\ImageFactory | \PHPUnit_Framework_MockObject_MockObject */
    protected $imageMock;

    /** @var \Magento\Eav\Model\Config | \PHPUnit_Framework_MockObject_MockObject */
    protected $eavConfigMock;

    /** @var \Magento\Catalog\Model\ProductRepository | \PHPUnit_Framework_MockObject_MockObject */
    protected $productRepositoryMock;

    /** @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable | \PHPUnit_Framework_MockObject_MockObject */
    protected $configurableMock;

    /** @var \Magento\Catalog\Api\CategoryRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject */
    protected $categoryRepositoryMock;

    /** @var Magento\Store\Model\Store  | \PHPUnit_Framework_MockObject_MockObject */
    protected $storeMock;

    /** @var ScopeConfigInterface | \PHPUnit_Framework_MockObject_MockObject */
    protected $scopeConfigMock;

    /** @var Attribute | \PHPUnit_Framework_MockObject_MockObject */
    protected $attributeMock;

    /** @var ScopeConfigInterface | \PHPUnit_Framework_MockObject_MockObject */
    protected $productMock;

    /** @var Magento\Catalog\Model\Product */
    protected $productHelper;

    public function testGetAttributeValueWhenAttributeIsSelect()
    {
        $this->scopeConfigMock->method('getValue')->with(
            Product::PATH_DATA_TRANSFER_MANUFACTURER, 'store', self::STORE_ID
        )->willReturn(self::TEST_ATTRIBUTE_CODE);
        $this->productMock->method('getAttributeText')->with(self::TEST_ATTRIBUTE_CODE)->willReturn(
            self::SELECT_TEST_PRODUCT_VALUE
        );
        $this->productMock->expects($this->once())->method('getAttributeText');
        $this->eavConfigMock->method('getAttribute')->with('catalog_product', self::TEST_ATTRIBUTE_CODE)->willReturn(
            $this->attributeMock
        );
        $this->contextMock->method('getScopeConfig')->willReturn($this->scopeConfigMock);
        $this->attributeMock->method('getFrontendInput')->willReturn('select');
        $this->attributeMock->expects($this->once())->method('getFrontendInput');

        $result = $this->productHelper->get('Manufacturer', $this->productMock, $this->storeMock);

        $this->assertNotEquals(self::SELECT_TEST_OPTION_ID, $result);
        $this->assertEquals(self::SELECT_TEST_PRODUCT_VALUE, $result);
    }

    public function testGetAttributeValueWhenAttributeIsVarchar()
    {
        $this->scopeConfigMock->method('getValue')->with(Product::PATH_DATA_TRANSFER_EAN, 'store', self::STORE_ID)
            ->willReturn(self::TEST_ATTRIBUTE_CODE);
        $this->attributeMock->method('getFrontendInput')->willReturn('text');
        $this->productMock->method('getData')->with(self::TEST_ATTRIBUTE_CODE)->willReturn(
            self::VARCHAR_TEST_PRODUCT_VALUE
        );
        $this->eavConfigMock->method('getAttribute')->with('catalog_product', self::TEST_ATTRIBUTE_CODE)->willReturn(
            $this->attributeMock
        );
        $this->contextMock->method('getScopeConfig')->willReturn($this->scopeConfigMock);
        $this->productMock->method('getData')->with(self::TEST_ATTRIBUTE_CODE)->willReturn(
            self::VARCHAR_TEST_PRODUCT_VALUE
        );
        $this->attributeMock->expects($this->once())->method('getFrontendInput');
        $this->productMock->expects($this->never())->method('getAttributeText');
        $this->productMock->expects($this->once())->method('getData');

        $result = $this->productHelper->get('EAN', $this->productMock, $this->storeMock);

        $this->assertEquals(self::VARCHAR_TEST_PRODUCT_VALUE, $result);
    }

    public function testGetAttributeValueWhenAttributeIsMultiselect()
    {
        $this->scopeConfigMock->method('getValue')->with(
            Product::PATH_DATA_TRANSFER_MANUFACTURER, 'store', self::STORE_ID
        )->willReturn(self::TEST_ATTRIBUTE_CODE);
        $this->attributeMock->method('getFrontendInput')->willReturn('multiselect');
        $this->productMock->method('getAttributeText')->with(self::TEST_ATTRIBUTE_CODE)->willReturn(
            self::MULTI_SELECT_TEST_PRODUCT_VALUE
        );
        $this->eavConfigMock->method('getAttribute')->with('catalog_product', self::TEST_ATTRIBUTE_CODE)->willReturn(
            $this->attributeMock
        );
        $this->contextMock->method('getScopeConfig')->willReturn($this->scopeConfigMock);
        $this->productMock->expects($this->once())->method('getAttributeText');
        $this->attributeMock->expects($this->once())->method('getFrontendInput');

        $result = $this->productHelper->get('Manufacturer', $this->productMock, $this->storeMock);

        $this->assertNotEquals(self::MULTI_SELECT_TEST_OPTION_ID, $result);
        $this->assertEquals(self::MULTI_SELECT_TEST_PRODUCT_VALUE, $result);
    }

    public function testGetAttributeValueWhenAttributeIsBoolean()
    {
        $this->scopeConfigMock->method('getValue')->with(
            Product::PATH_DATA_TRANSFER_MANUFACTURER, 'store', self::STORE_ID
        )->willReturn(self::TEST_ATTRIBUTE_CODE);
        $this->attributeMock->method('getFrontendInput')->willReturn('boolean');
        $this->productMock->method('getAttributeText')->with(self::TEST_ATTRIBUTE_CODE)->willReturn(
            self::BOOLEAN_TEST_PRODUCT_VALUE_LABEL
        );
        $this->eavConfigMock->method('getAttribute')->with('catalog_product', self::TEST_ATTRIBUTE_CODE)->willReturn(
            $this->attributeMock
        );
        $this->contextMock->method('getScopeConfig')->willReturn($this->scopeConfigMock);
        $this->productMock->expects($this->once())->method('getAttributeText');
        $this->attributeMock->expects($this->once())->method('getFrontendInput');

        $result = $this->productHelper->get('Manufacturer', $this->productMock, $this->storeMock);

        $this->assertNotEquals(self::BOOLEAN_TEST_PRODUCT_VALUE, $result);
        $this->assertEquals(self::BOOLEAN_TEST_PRODUCT_VALUE_LABEL, $result);
    }

    public function testAttributeValueHTMLTagsAreDecoded()
    {
        $this->scopeConfigMock->method('getValue')->with(Product::PATH_DATA_TRANSFER_EAN, 'store', self::STORE_ID)
            ->willReturn(self::TEST_ATTRIBUTE_CODE);
        $this->attributeMock->method('getFrontendInput')->willReturn('text');
        $this->productMock->method('getData')->with(self::TEST_ATTRIBUTE_CODE)->willReturn(
            self::HTML_TAGS_ATTRIBUTE_VALUE
        );
        $this->eavConfigMock->method('getAttribute')->with('catalog_product', self::TEST_ATTRIBUTE_CODE)->willReturn(
            $this->attributeMock
        );
        $this->contextMock->method('getScopeConfig')->willReturn($this->scopeConfigMock);
        $this->attributeMock->expects($this->once())->method('getFrontendInput');
        $this->productMock->expects($this->never())->method('getAttributeText');
        $this->productMock->expects($this->once())->method('getData');

        $result   = $this->productHelper->get('EAN', $this->productMock, $this->storeMock);
        $expected = \html_entity_decode(self::HTML_TAGS_ATTRIBUTE_VALUE);

        $this->assertNotEquals(self::VARCHAR_TEST_PRODUCT_VALUE, $result);
        $this->assertEquals($expected, $result, 'Attribute value should be decoded');
    }

    public function testGetAttributeValueWhenNoAttributeIsConfigured()
    {
        $this->scopeConfigMock->method('getValue')->with(Product::PATH_DATA_TRANSFER_EAN, 'store', self::STORE_ID)
            ->willReturn(null);
        $this->productMock->method('getData')->with(self::TEST_ATTRIBUTE_CODE)->willReturn(
            self::VARCHAR_TEST_PRODUCT_VALUE
        );
        $this->contextMock->method('getScopeConfig')->willReturn($this->scopeConfigMock);
        $this->productMock->expects($this->never())->method('getAttributeText');
        $this->eavConfigMock->expects($this->once())->method('getAttribute')->with('catalog_product', null)
            ->willThrowException(new LocalizedException(new Phrase('')));

        $result = $this->productHelper->get('EAN', $this->productMock, $this->storeMock);

        $this->assertNull($result);
    }

    protected function setUp()
    {
        $this->contextMock            = $this->createMock(Context::class);
        $this->imageMock              = $this->createMock(ImageFactory::class);
        $this->configurableMock       = $this->createMock(Configurable::class);
        $this->categoryRepositoryMock = $this->createMock(CategoryRepositoryInterface::class);
        $this->storeMock              = $this->createMock(StoreInterface::class);
        $this->productRepositoryMock  = $this->createMock(ProductRepository::class);
        $this->eavConfigMock          = $this->createMock(Config::class);
        $this->scopeConfigMock        = $this->createMock(ScopeConfigInterface::class);
        $this->attributeMock          = $this->createMock(Attribute::class);
        $this->productMock            = $this->createMock(\Magento\Catalog\Model\Product::class);

        $this->storeMock->method('getId')->willReturn(self::STORE_ID);
        $this->contextMock->method('getScopeConfig')->willReturn($this->scopeConfigMock);
        $this->productHelper = (new ObjectManager($this))->getObject(
        \Omikron\Factfinder\Helper\Product::class,
        [
            'context'                        => $this->contextMock,
            'imageHelperFactory'             => $this->imageMock,
            'eavConfig'                      => $this->eavConfigMock,
            'productRepository'              => $this->productRepositoryMock,
            'catalogProductTypeConfigurable' => $this->configurableMock,
            'categoryRepository'             => $this->categoryRepositoryMock
        ]
    );
    }
}
