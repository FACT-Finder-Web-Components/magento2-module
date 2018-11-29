<?php
// @codingStandardsIgnoreFile

namespace Omikron\Factfinder\Test\Unit\Helper;

use Magento\Catalog\Model\Entity\Attribute;
use Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\App\Helper\Context;
use \Magento\Catalog\Helper\Image;
use \Magento\Eav\Model\Config;
use \Magento\Catalog\Model\ProductRepository;
use \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use \Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Api\Data\StoreInterface;
use Omikron\Factfinder\Helper\Product;

/**
 * Class ProductTest
 */
class ProductTest extends \PHPUnit\Framework\TestCase
{
    const STORE_ID                       = 1;
    const TEST_ATTRIBUTE_CODE            = 'test';
    const VARCHAR_TEST_PRODUCT_VALUE     = 'test value';
    const SELECT_TEST_PRODUCT_VALUE      = 'option label';
    const MULTISELECT_TEST_PRODUCT_VALUE = 'option label1, option label2';
    const SELECT_TEST_OPTION_ID          = 1;
    const MULTISELECT_TEST_OPTION_ID     = '1,2';
    const HTML_TAGS_ATTRIBUTE_VALUE      = '&lt;div class=&quot;test description&quot;&gt; This is test attribute value &lt;/div&gt;';

    /**
     * @var \Magento\Framework\App\Helper\Context  | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Catalog\Helper\Image  | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $imageMock;

    /**
     * @var \Magento\Eav\Model\Config  | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eavConfigMock;

    /**
     * @var \Magento\Catalog\Model\ProductRepository  | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productRepositoryMock;

    /**
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurableMock;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface  | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryRepositoryMock;

    /**
     * @var Magento\Store\Model\Store  | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \Omikron\Factfinder\Helper\Product
     */
    protected $productHelper;

    /**
     * Setup the test
     */
    public function setUp()
    {
        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->imageMock = $this->getMockBuilder(Image::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->configurableMock = $this->getMockBuilder(Configurable::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->categoryRepositoryMock = $this->getMockBuilder(CategoryRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->storeMock = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->storeMock->method('getId')
            ->willReturn(self::STORE_ID);

        $this->productRepositoryMock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eavConfigMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->createTestProductHelperInstance();
    }

    /**
     * @param \Magento\Catalog\Model\Product  | \PHPUnit_Framework_MockObject_MockObject $productMock
     * @param \Magento\Catalog\Model\Entity\Attributt  | \PHPUnit_Framework_MockObject_MockObject $attributeMock
     * @param \Magento\Framework\App\Config\ScopeConfigInterface | \PHPUnit_Framework_MockObject_MockObject $scopeConfigMock
     *
     * @dataProvider manufacturerProductDataProvider
     */
    public function testGetAttributeValueWhenAttributeIsSelect($productMock, $attributeMock, $scopeConfigMock)
    {
        $attributeMock->method('getFrontendInput')
            ->willReturn('select');

        $productMock->method('getAttributeText')
            ->with(self::TEST_ATTRIBUTE_CODE)
            ->willReturn(self::SELECT_TEST_PRODUCT_VALUE);

        $this->eavConfigMock->method('getAttribute')
            ->with(
                'catalog_product',
                self::TEST_ATTRIBUTE_CODE
            )
            ->willReturn($attributeMock);

        $this->contextMock->method('getScopeConfig')
            ->willReturn($scopeConfigMock);

        $this->createTestProductHelperInstance();

        $productMock->expects($this->once())
            ->method('getAttributeText');

        $attributeMock->expects($this->once())
            ->method('getFrontendInput');

        $result = $this->productHelper->get('Manufacturer', $productMock, $this->storeMock);

        $this->assertNotEquals(self::SELECT_TEST_OPTION_ID, $result);
        $this->assertEquals(self::SELECT_TEST_PRODUCT_VALUE, $result);
    }

    /**
     * @param \Magento\Catalog\Model\Product  | \PHPUnit_Framework_MockObject_MockObject $productMock
     * @param \Magento\Catalog\Model\Entity\Attributt  | \PHPUnit_Framework_MockObject_MockObject $attributeMock
     * @param \Magento\Framework\App\Config\ScopeConfigInterface | \PHPUnit_Framework_MockObject_MockObject $scopeConfigMock
     *
     * @dataProvider eanProductDataProvider
     */
    public function testGetAttributeValueWhenAttributeIsVarchar($productMock, $attributeMock, $scopeConfigMock)
    {
        $attributeMock->method('getFrontendInput')
            ->willReturn('text');

        $productMock->method('getData')
            ->with(self::TEST_ATTRIBUTE_CODE)
            ->willReturn(self::VARCHAR_TEST_PRODUCT_VALUE);

        $this->eavConfigMock->method('getAttribute')
            ->with(
                'catalog_product',
                self::TEST_ATTRIBUTE_CODE
            )
            ->willReturn($attributeMock);

        $this->contextMock->method('getScopeConfig')
            ->willReturn($scopeConfigMock);

        $productMock->method('getData')
            ->with(self::TEST_ATTRIBUTE_CODE)
            ->willReturn(self::VARCHAR_TEST_PRODUCT_VALUE);

        $this->createTestProductHelperInstance();

        $attributeMock->expects($this->once())
            ->method('getFrontendInput');

        $productMock->expects($this->never())
            ->method('getAttributeText');

        $productMock->expects($this->once())
            ->method('getData');

        $result = $this->productHelper->get('EAN', $productMock, $this->storeMock);

        $this->assertEquals(self::VARCHAR_TEST_PRODUCT_VALUE, $result);
    }

    /**
     * @param \Magento\Catalog\Model\Product  | \PHPUnit_Framework_MockObject_MockObject $productMock
     * @param \Magento\Catalog\Model\Entity\Attributt  | \PHPUnit_Framework_MockObject_MockObject $attributeMock
     * @param \Magento\Framework\App\Config\ScopeConfigInterface | \PHPUnit_Framework_MockObject_MockObject $scopeConfigMock
     *
     * @dataProvider manufacturerProductDataProvider
     */
    public function testGetAttributeValueWhenAttributeIsMultiselect($productMock, $attributeMock, $scopeConfigMock)
    {
        $attributeMock->method('getFrontendInput')
            ->willReturn('multiselect');

        $productMock->method('getAttributeText')
            ->with(self::TEST_ATTRIBUTE_CODE)
            ->willReturn(self::MULTISELECT_TEST_PRODUCT_VALUE);

        $this->eavConfigMock->method('getAttribute')
            ->with(
                'catalog_product',
                self::TEST_ATTRIBUTE_CODE
            )
            ->willReturn($attributeMock);

        $this->contextMock->method('getScopeConfig')
            ->willReturn($scopeConfigMock);

        $this->createTestProductHelperInstance();

        $productMock->expects($this->once())
            ->method('getAttributeText');

        $attributeMock->expects($this->once())
            ->method('getFrontendInput');

        $result = $this->productHelper->get('Manufacturer', $productMock, $this->storeMock);

        $this->assertNotEquals(self::MULTISELECT_TEST_OPTION_ID, $result);
        $this->assertEquals(self::MULTISELECT_TEST_PRODUCT_VALUE, $result);
    }

    /**
     * @param \Magento\Catalog\Model\Product  | \PHPUnit_Framework_MockObject_MockObject $productMock
     * @param \Magento\Catalog\Model\Entity\Attributt  | \PHPUnit_Framework_MockObject_MockObject $attributeMock
     * @param \Magento\Framework\App\Config\ScopeConfigInterface | \PHPUnit_Framework_MockObject_MockObject $scopeConfigMock
     *
     * @dataProvider manufacturerProductDataProvider
     */
    public function testGetAttributeValueWhenAttributeIsBoolean($productMock, $attributeMock, $scopeConfigMock)
    {
        $attributeMock->method('getFrontendInput')
            ->willReturn('boolean');

        $productMock->method('getAttributeText')
            ->with(self::TEST_ATTRIBUTE_CODE)
            ->willReturn(self::BOOLEAN_TEST_PRODUCT_VALUE_LABEL);

        $this->eavConfigMock->method('getAttribute')
            ->with(
                'catalog_product',
                self::TEST_ATTRIBUTE_CODE
            )
            ->willReturn($attributeMock);

        $this->contextMock->method('getScopeConfig')
            ->willReturn($scopeConfigMock);

        $this->createTestProductHelperInstance();

        $productMock->expects($this->once())
            ->method('getAttributeText');

        $attributeMock->expects($this->once())
            ->method('getFrontendInput');

        $result = $this->productHelper->get('Manufacturer', $productMock, $this->storeMock);

        $this->assertNotEquals(self::BOOLEAN_TEST_PRODUCT_VALUE, $result);
        $this->assertEquals(self::BOOLEAN_TEST_PRODUCT_VALUE_LABEL, $result);
    }

    /**
     * @param \Magento\Catalog\Model\Product  | \PHPUnit_Framework_MockObject_MockObject $productMock
     * @param \Magento\Catalog\Model\Entity\Attributt  | \PHPUnit_Framework_MockObject_MockObject $attributeMock
     * @param \Magento\Framework\App\Config\ScopeConfigInterface | \PHPUnit_Framework_MockObject_MockObject $scopeConfigMock
     *
     * @dataProvider eanProductDataProvider
     */
    public function testAttributeValueHTMLTagsAreDecoded($productMock, $attributeMock, $scopeConfigMock)
    {
        $attributeMock->method('getFrontendInput')
            ->willReturn('text');

        $productMock->method('getData')
            ->with(self::TEST_ATTRIBUTE_CODE)
            ->willReturn(self::HTML_TAGS_ATTRIBUTE_VALUE);

        $this->eavConfigMock->method('getAttribute')
            ->with(
                'catalog_product',
                self::TEST_ATTRIBUTE_CODE
            )
            ->willReturn($attributeMock);

        $this->contextMock->method('getScopeConfig')
            ->willReturn($scopeConfigMock);

        $this->createTestProductHelperInstance();

        $attributeMock->expects($this->once())
            ->method('getFrontendInput');

        $productMock->expects($this->never())
            ->method('getAttributeText');

        $productMock->expects($this->once())
            ->method('getData');

        $result = $this->productHelper->get('EAN', $productMock, $this->storeMock);
        $expected = \html_entity_decode(self::HTML_TAGS_ATTRIBUTE_VALUE);

        $this->assertNotEquals(self::VARCHAR_TEST_PRODUCT_VALUE, $result);
        $this->assertEquals($expected, $result);
    }

    /**
     * @param \Magento\Catalog\Model\Product  | \PHPUnit_Framework_MockObject_MockObject $productMock
     * @param \Magento\Catalog\Model\Entity\Attributt  | \PHPUnit_Framework_MockObject_MockObject $attributeMock
     * @param \Magento\Framework\App\Config\ScopeConfigInterface | \PHPUnit_Framework_MockObject_MockObject $scopeConfigMock
     *
     * @dataProvider emptyProductDataProvider
     */
    public function testGetAttributeValueWhenNoAttributeIsConfigured($productMock, $attributeMock, $scopeConfigMock)
    {
        $productMock->method('getData')
            ->with(self::TEST_ATTRIBUTE_CODE)
            ->willReturn(self::VARCHAR_TEST_PRODUCT_VALUE);

        $this->contextMock->method('getScopeConfig')
            ->willReturn($scopeConfigMock);

        $this->createTestProductHelperInstance();

        $productMock->expects($this->never())
            ->method('getAttributeText');

        $productMock->expects($this->once())
            ->method('getData');

        $this->eavConfigMock->expects($this->once())
            ->method('getAttribute')
            ->with(
                'catalog_product',
                null
            )
            ->willThrowException( new LocalizedException(new Phrase("")));

        $result = $this->productHelper->get('EAN', $productMock, $this->storeMock);
        $this->assertNull($result);
    }

    /**
     * @return array
     */
    public function manufacturerProductDataProvider()
    {
        $base = $this->baseProductDataProvider();
        $base[0]['scopeConfigMock']->method('getValue')
            ->with(
                Product::PATH_DATA_TRANSFER_MANUFACTURER,
                'store',
                self::STORE_ID
            )
            ->willReturn(self::TEST_ATTRIBUTE_CODE);

        return $base;
    }

    /**
     * @return array
     */
    public function eanProductDataProvider()
    {
        $base = $this->baseProductDataProvider();
        $base[0]['scopeConfigMock']->method('getValue')
            ->with(
                Product::PATH_DATA_TRANSFER_EAN,
                'store',
                self::STORE_ID
            )
            ->willReturn(self::TEST_ATTRIBUTE_CODE);

        return $base;
    }

    /**
     * @return array
     */
    public function emptyProductDataProvider()
    {
        $base = $this->baseProductDataProvider();
        $base[0]['scopeConfigMock']->method('getValue')
            ->with(
                Product::PATH_DATA_TRANSFER_EAN,
                'store',
                self::STORE_ID
            )
            ->willReturn(null);

        return $base;
    }

    /**
     * @return array
     */
    public function baseProductDataProvider()
    {
        $attributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();

        $scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productMock = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
            ->disableOriginalConstructor()
            ->getMock();

        return [
            [
                'productMock'     => $productMock,
                'attributeMock'   => $attributeMock,
                'scopeConfigMock' => $scopeConfigMock
            ]
        ];
    }


    /**
     * Create Omikron\Factfinder\Helper\Product instance
     */
    protected function createTestProductHelperInstance()
    {
        $this->productHelper = (new ObjectManager($this))
            ->getObject(
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
