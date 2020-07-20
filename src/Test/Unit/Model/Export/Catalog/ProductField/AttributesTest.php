<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductField;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Omikron\Factfinder\Model\Filter\TextFilter;
use Omikron\Factfinder\Model\Formatter\NumberFormatter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AttributesTest extends TestCase
{
    /** @var MockObject|ScopeConfigInterface */
    private $config;

    /** @var MockObject|ProductResource */
    private $resource;

    /** @var Attributes */
    private $testee;

    public function test_exception_is_thrown_if_the_attribute_is_not_exportable()
    {
        $this->expectException(\UnexpectedValueException::class);

        $product   = $this->createConfiguredMock(Product::class, ['getDataUsingMethod' => [1, 2, 3]]);
        $attribute = $this->createConfiguredMock(Attribute::class, [
            'getAttributeCode' => 'foobar',
            'getStoreLabel'    => 'Foobar',
        ]);

        $this->config->method('getValue')->willReturn('foobar');
        $this->resource->method('getAttribute')->with('foobar')->willReturn($attribute);

        $this->testee->getValue($product);
    }

    protected function setUp()
    {
        $this->config   = $this->createMock(ScopeConfigInterface::class);
        $this->resource = $this->createMock(ProductResource::class);

        $this->testee = new Attributes(
            $this->config,
            $this->resource,
            new TextFilter(),
            new NumberFormatter(),
            'foo/bar/baz'
        );
    }
}
