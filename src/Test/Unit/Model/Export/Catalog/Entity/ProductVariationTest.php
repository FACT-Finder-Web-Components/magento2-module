<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\Entity;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Omikron\Factfinder\Model\Export\Catalog\FieldProvider;
use Omikron\Factfinder\Model\Export\Catalog\ProductField\ProductImage;
use Omikron\Factfinder\Model\Formatter\NumberFormatter;
use PHPUnit\Framework\TestCase;

/**
 * @covers ProductVariation
 */
class ProductVariationTest extends TestCase
{
    public function test_variant_data_will_override_the_parent()
    {
        $configurableProductData = [
            'ProductNumber' => 'sku-configurable',
            'Price'         => '8.99',
            'HasVariants'   => 1,
            'MagentoId'     => 1,
            'ImageUrl'      => 'http://random-variant-image.png'
        ];

        $variantMock = $this->createConfiguredMock(
            Product::class,
            [
                'getSku'        => 'sku-variant',
                'getFinalPrice' => '9.99',
                'isAvailable'   => true,
                'getId'         => 2
            ]
        );

        $fieldProviderMock = $this->createConfiguredMock(
            FieldProvider::class,
            [
                'getVariantFields' => [
                    'image_url' => $this->createConfiguredMock(
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
            $variantMock,
            $this->createMock(Product::class),
            new NumberFormatter(),
            $fieldProviderMock,
            $configurableProductData
        );

        $this->assertEquals(
            [
                'ProductNumber' => 'sku-variant',
                'Price'         => '9.99',
                'Availability'  => 1,
                'HasVariants'   => 0,
                'MagentoId'     => 2,
                'ImageUrl'      => 'http://specific-variant-image.png'
            ], $productVariation->toArray()
        );
    }
}
