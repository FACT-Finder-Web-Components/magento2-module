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
        $storeId = (int) $product->getStoreId();
        $values  = '';
        foreach ($this->getAttributes($storeId) as $attribute) {
            $label = $this->filter->filterValue($attribute->getStoreLabel($storeId));
            foreach ($this->valuesExtractor->getAttributeValues($product, $attribute) as $value) {
                $values .= "|{$label}={$value}";
            }
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
        $attributes = $this->exportConfig->getMultiAttributes($storeId);
        return array_filter(array_map([$this->productResource, 'getAttribute'], $attributes));
    }
}