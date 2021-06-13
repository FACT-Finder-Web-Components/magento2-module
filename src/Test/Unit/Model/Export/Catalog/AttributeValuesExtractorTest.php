<?php

namespace Omikron\Factfinder\Model\Export\Catalog;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Email\Model\Template\Filter;
use Omikron\Factfinder\Api\Filter\FilterInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Omikron\Factfinder\Model\Formatter\NumberFormatter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AttributeValuesExtractorTest extends TestCase
{
    private $filterMock;
    private $numberFormatter;
    private $attributeExtractor;

    public function test_it_return_empty_string_on_null_value()
    {
        $productMock = $this->createMock(Product::class, ['getSku' => 'sku-1']);
        $attributeMock = $this->createMock(Attribute::class, []);
        $attributeValues = $this->attributeExtractor->getAttributeValues($productMock, $attributeMock);
        $this->assertEquals([], $attributeValues);
    }

    protected function setUp(): void
    {
        $this->filterMock = $this->createMock(FilterInterface::class, []);
        $this->numberFormatter = $this->createMock(NumberFormatter::class, []);
        $this->attributeExtractor = new AttributeValuesExtractor($this->filterMock, $this->numberFormatter);
    }
}
