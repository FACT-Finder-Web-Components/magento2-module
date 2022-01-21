<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\Entity;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Model\AbstractModel;
use Omikron\Factfinder\Model\Export\Catalog\FieldProvider;
use Omikron\Factfinder\Model\Export\Catalog\ProductField\FilterAttributes;
use Omikron\Factfinder\Model\Export\Catalog\ProductField\ProductImage;
use Omikron\Factfinder\Model\Formatter\NumberFormatter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers ProductVariation
 */
class ProductVariationTest extends TestCase
{
    /** @var MockObject|AbstractModel */
    private $variantMock;

    private $configurableProductData = [
        'ProductNumber' => 'sku-configurable',
        'Price'         => '8.99',
        'HasVariants'   => 1,
        'MagentoId'     => 1,
        'ImageUrl'      => 'http://random-variant-image.png',
    ];

    public function test_variant_data_will_override_the_parent()
    {
        $fieldProviderMock = $this->createConfiguredMock(
            FieldProvider::class,
            [
                'getVariantFields' => [
                    'ImageUrl' => $this->createConfiguredMock(
                        ProductImage::class,
                        [
                            'getName'  => 'ImageUrl',
                            'getValue' => 'http://specific-variant-image.png'
                        ]
                    )
                ]
            ]
        );

        $productVariation = new ProductVariation(
            $this->variantMock,
            $this->createMock(Product::class),
            new NumberFormatter(),
            $fieldProviderMock,
            $this->configurableProductData
        );

        $this->containsSubset(
            [
                'ProductNumber' => 'sku-variant',
                'Price'         => '9.99',
                'Availability'  => 1,
                'MagentoId'     => 2,
                'HasVariants'   => 0,
                'ImageUrl'      => 'http://specific-variant-image.png',
            ], $productVariation->toArray()
        );
    }

    public function test_configurable_attributes_should_be_merged_with_filter_attributes()
    {
        $fieldProviderMock = $this->createConfiguredMock(
            FieldProvider::class,
            [
                'getVariantFields' => [
                    'FilterAttributes' => $this->createConfiguredMock(
                        FilterAttributes::class,
                        [
                            'getName'  => 'FilterAttributes',
                            'getValue' => '|Eco Collection=No|New=No|Price=52.00|Quantity=In Stock|'
                        ]
                    )
                ]
            ]
        );

        $productVariation = new ProductVariation(
            $this->variantMock,
            $this->createMock(Product::class),
            new NumberFormatter(),
            $fieldProviderMock,
            ['FilterAttributes' => '|Color=Red|Size=XS|'] + $this->configurableProductData
        );

        $this->assertEquals('|Color=Red|Size=XS|Eco Collection=No|New=No|Price=52.00|Quantity=In Stock|',
                            $productVariation->toArray()['FilterAttributes']
        );
    }

    protected function setUp(): void
    {
        $this->variantMock = $variantMock = $this->createConfiguredMock(
            Product::class,
            [
                'getSku'        => 'sku-variant',
                'getFinalPrice' => '9.99',
                'isAvailable'   => true,
                'getId'         => 2
            ]
        );
    }

    private function containsSubset(array $expected, array $actual)
    {
        return $this->assertEquals($expected + $this->configurableProductData, $actual);
    }
}
