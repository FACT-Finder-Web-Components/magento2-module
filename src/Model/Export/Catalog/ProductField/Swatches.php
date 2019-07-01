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
        $options = $this->productType->getConfigurableOptions($product);
        $attributes = $this->productType->getConfigurableAttributes($product)->getItems();

        if (empty($options)) {
            return '';
        }

        return $this->serializer->serialize([
            'product_id' => $product->getId(),
            'attributes' =>
                array_reduce($attributes, function ($attributes, Attribute $attribute) use ($options) {
                    $attributes[$attribute->getAttributeId()] = [
                        'attribute_label' => $attribute->getProductAttribute()->getFrontendLabel(),
                        'attribute_id'    => $attribute->getAttributeId(),
                        'position'        => $attribute->getPosition(),
                        'options'         =>
                            array_reduce($options[$attribute->getAttributeId()], function ($options, array $option) use ($attribute) {
                                ['product_id'   => $productId, 'option_title' => $label, 'value_index'  => $optionId] = $option;
                                $options[$optionId] = $this->addSwatches((int) $optionId) + [
                                        'product_id'   => $productId,
                                        'attribute_id' => $attribute->getAttributeId(),
                                        'value'        => $label,
                                        'option_id'    => $optionId
                                    ];

                                return $options;
                            })
                    ];

                    return $attributes;
                })
        ]);
    }

    private function addSwatches(int $optionId): array
    {
        $swatchData = $this->swatchHelper->getSwatchesByOptionsId([$optionId]);
        return isset($swatchData[$optionId]) ? array_intersect_key($swatchData[$optionId], array_flip(['option_id', 'type', 'value'])) : [];
    }
}
