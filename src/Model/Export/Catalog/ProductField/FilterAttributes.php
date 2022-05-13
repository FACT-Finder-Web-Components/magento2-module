<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductField;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\Model\AbstractModel;
use Omikron\Factfinder\Api\Export\FieldInterface;
use Omikron\Factfinder\Api\Filter\FilterInterface;
use Omikron\Factfinder\Model\Config\ExportConfig;
use Omikron\Factfinder\Model\Export\Catalog\AttributeValuesExtractor;

class FilterAttributes implements FieldInterface
{
    protected bool $numerical = false;
    protected string $name = 'FilterAttributes';
    private ExportConfig $exportConfig;
    private ProductResource $productResource;
    private FilterInterface $filter;
    private AttributeValuesExtractor $valuesExtractor;

    /**
     *  [storeId => string[]]
     */
    private array $attributes = [];

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
        return $this->name;
    }

    public function getValue(AbstractModel $product): string
    {
        $values = '';
        foreach ($this->getAttributes((int) $product->getStoreId()) as $label => $attribute) {
            $attributeValues = implode('#', $this->valuesExtractor->getAttributeValues($product, $attribute));
            if ($attributeValues) {
                $values .= "|{$label}={$attributeValues}";
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
        if (!isset($this->attributes[$storeId])) {
            $codes      = $this->exportConfig->getMultiAttributes($storeId, $this->numerical);
            $attributes = array_filter(array_map([$this->productResource, 'getAttribute'], $codes));
            $labels     = array_map($this->getAttributeLabel($storeId), $attributes);

            $this->attributes[$storeId] = array_combine($labels, $attributes);
        }
        return $this->attributes[$storeId];
    }

    private function getAttributeLabel(int $storeId): callable
    {
        return fn (Attribute $attribute): string => $this->filter->filterValue((string) $attribute->getStoreLabel($storeId));
    }
}
