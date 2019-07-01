<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductField;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProductType;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Swatches\Helper\Data as SwatchHelper;
use Omikron\Factfinder\Api\Export\Catalog\ProductFieldInterface;

class Swatches implements ProductFieldInterface
{
    /** @var SwatchHelper */
    private $swatchHelper;

    /** @var ConfigurableProductType */
    private $productType;

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(
        SwatchHelper $swatchHelper,
        ConfigurableProductType $productType,
        SerializerInterface $serializer
    ) {
        $this->swatchHelper = $swatchHelper;
        $this->productType  = $productType;
        $this->serializer   = $serializer;
    }

    public function getValue(Product $product): string
    {
        $configurableOptions    = $this->productType->getConfigurableOptions($product);
        $configurableAttributes = $this->productType->getConfigurableAttributes($product)->getItems();

        return empty($configurableOptions) ? '' : $this->serializer->serialize(
            [
                'product_id' => $product->getId(),
                'attributes' => array_reduce(
                    $configurableAttributes,
                    function ($attributes, Attribute $attribute) use ($product, $configurableOptions) {
                        $attributes[$attribute->getAttributeId()] = [
                            'attribute_label' => $attribute->getProductAttribute()->getFrontendLabel(),
                            'attribute_id'    => $attribute->getAttributeId(),
                            'position'        => $attribute->getPosition(),
                            'options'         => array_reduce(
                                $configurableOptions[$attribute->getAttributeId()],
                                function ($options, array $option) use ($attribute) {
                                    [
                                        'product_id'   => $productId,
                                        'option_title' => $optionLabel,
                                        'value_index'  => $value
                                    ] = $option;
                                    $options[$value] = [
                                            'product_id'   => $productId,
                                            'attribute_id' => $attribute->getAttributeId(),
                                            'option_label' => $optionLabel,
                                        ] + $this->addSwatches((int) $value);

                                    return $options;
                                }
                            )
                        ];

                        return $attributes;
                    }
                )
            ]
        );
    }

    private function addSwatches(int $optionId): array
    {
        return array_intersect_key($this->swatchHelper->getSwatchesByOptionsId([$optionId])[$optionId], array_flip(['option_id', 'type', 'value']));
    }
}
