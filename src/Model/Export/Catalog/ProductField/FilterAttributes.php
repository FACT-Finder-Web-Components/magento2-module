<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductField;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Omikron\Factfinder\Api\Export\Catalog\ProductFieldInterface;
use Omikron\Factfinder\Api\Filter\FilterInterface;
use Omikron\Factfinder\Model\Config\ExportConfig;
use Omikron\Factfinder\Model\Export\Catalog\AttributeValuesExtractor;

class FilterAttributes implements ProductFieldInterface
{
    /** @var ExportConfig */
    private $exportConfig;

    /** @var ProductResource */
    private $productResource;

    /** @var Attribute[][] */
    private $attributes = [];

    /** @var FilterInterface */
    private $filter;

    /** @var AttributeValuesExtractor */
    private $valuesExtractor;

    public function __construct(
        ExportConfig $exportConfig,
        ProductResource $productResource,
        FilterInterface $filter,
        AttributeValuesExtractor $valuesExtractor
    ) {
        $this->exportConfig    = $exportConfig;
        $this->productResource = $productResource;
        $this->filter          = $filter;
        $this->valuesExtractor = $valuesExtractor;
    }

    public function getName(): string
    {
        return 'FilterAttributes';
    }

    public function getValue(Product $product): string
    {
        $values = '';
        foreach ($this->getAttributes((int) $product->getStoreId()) as $label => $attribute) {
            $attributeValues = implode('#', $this->valuesExtractor->getAttributeValues($product, $attribute));
            if ($attributeValues) $values .= "|{$label}={$attributeValues}";
        }
        return $values ? "{$values}|" : '';
    }

    /**
     * @param int $storeId
     *
     * @return Attribute[]
     */
    private function getAttributes(int $storeId): array
    {
        if (!isset($this->attributes[$storeId])) {
            $codes      = $this->exportConfig->getMultiAttributes($storeId);
            $attributes = array_filter(array_map([$this->productResource, 'getAttribute'], $codes));
            $labels     = array_map($this->getAttributeLabel($storeId), $attributes);

            $this->attributes[$storeId] = array_combine($labels, $attributes);
        }
        return $this->attributes[$storeId];
    }

    private function getAttributeLabel(int $storeId): callable
    {
        return function (Attribute $attribute) use ($storeId): string {
            return $this->filter->filterValue((string) $attribute->getStoreLabel($storeId));
        };
    }
}
