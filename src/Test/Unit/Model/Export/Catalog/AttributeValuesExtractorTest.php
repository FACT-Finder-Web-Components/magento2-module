<?php

namespace Omikron\Factfinder\Model\Export\Catalog;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Email\Model\Template\Filter;
use Omikron\Factfinder\Api\Filter\FilterInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Omikron\Factfinder\Model\Filter\TextFilter;
use Omikron\Factfinder\Model\Formatter\NumberFormatter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AttributeValuesExtractorTest extends TestCase
{
    /** @var NumberFormatter|MockObject  */
    private $numberFormatter;

    /** @var AttributeValuesExtractor  */
    private $attributeExtractor;

    public function test_it_returns_scalar_value()
    {
        $attributeValue  = 'Value';
        $productMock = $this->createConfiguredMock(Product::class, [
            'getDataUsingMethod' => $attributeValue
        ]);
        $attributeMock = $this->createConfiguredMock(Attribute::class, [
            'getAttributeCode' => 'test-atttribute',
            'getFrontendInput' => 'varchar'
        ]);
        $attributeValues = $this->attributeExtractor->getAttributeValues($productMock, $attributeMock);
        $this->assertEquals([$attributeValue], $attributeValues);
    }

    public function test_it_returns_empty_string_on_null_value()
    {
        $productMock = $this->createConfiguredMock(Product::class, [
            'getDataUsingMethod' => null
        ]);
        $attributeMock   = $this->createConfiguredMock(Attribute::class, [
            'getAttributeCode' => 'test-atttribute',
            'getFrontendInput' => 'varchar'
        ]);
        $attributeValues = $this->attributeExtractor->getAttributeValues($productMock, $attributeMock);
        $this->assertEquals([], $attributeValues);
    }

    protected function setUp(): void
    {
        $this->filterMock = $this->createMock(FilterInterface::class, []);
        $this->numberFormatter = $this->createMock(NumberFormatter::class, []);
        $this->attributeExtractor = new AttributeValuesExtractor(new TextFilter(), $this->numberFormatter);
    }
}
