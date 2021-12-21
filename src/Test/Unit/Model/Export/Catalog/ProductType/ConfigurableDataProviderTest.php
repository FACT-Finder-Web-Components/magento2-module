<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductType;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProductType;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Omikron\Factfinder\Api\Filter\FilterInterface;
use Omikron\Factfinder\Model\Export\Catalog\Entity\ProductVariationFactory;
use Omikron\Factfinder\Model\Formatter\NumberFormatter;
use PHPUnit\Framework\TestCase;

class ConfigurableDataProviderTest extends TestCase
{
    /** @var ConfigurableDataProvider */
    private $configurableDataProvider;

    public function test_will_return_string_on_string_value()
    {
        $getValueOrEmptyStringMethod = $this->invokeMethod($this->configurableDataProvider, 'getValueOrEmptyString', ['test']);
        $this->assertEquals($getValueOrEmptyStringMethod, 'test');
    }

    public function test_will_return_empty_string_on_null_value()
    {
        $getValueOrEmptyStringMethod = $this->invokeMethod($this->configurableDataProvider, 'getValueOrEmptyString', [null]);
        $this->assertEquals($getValueOrEmptyStringMethod, '');
    }

    public function test_will_return_empty_string_on_bool_value()
    {
        $getValueOrEmptyStringMethod = $this->invokeMethod($this->configurableDataProvider, 'getValueOrEmptyString', [false]);
        $this->assertEquals($getValueOrEmptyStringMethod, '');
    }

    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(ProductRepositoryInterface::class);
        $this->numberFormatMock = $this->createMock(NumberFormatter::class);
        $this->configurableProductTypeMock = $this->createMock(ConfigurableProductType::class);
        $this->filterInterfaceMock = $this->createMock(FilterInterface::class);
        $this->variantFactoryMock = $this->getMockBuilder(ProductVariationFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->builderMock = $this->createMock(SearchCriteriaBuilder::class);

        $this->productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->configurableDataProvider = new ConfigurableDataProvider(
            $this->productMock,
            $this->numberFormatMock,
            $this->configurableProductTypeMock,
            $this->filterInterfaceMock,
            $this->variantFactoryMock,
            $this->repositoryMock,
            $this->builderMock
        );
    }
}
