<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductField;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface as Scope;
use Omikron\Factfinder\Api\Export\Catalog\ProductFieldInterface;
use Omikron\Factfinder\Api\Filter\FilterInterface;
use Omikron\Factfinder\Model\Export\Catalog\AttributeValuesExtractor;

class Attributes implements ProductFieldInterface
{
    private const CONFIG_PATH = 'factfinder/data_transfer/ff_additional_attributes';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var ProductResource */
    private $productResource;

    /** @var FilterInterface */
    private $filter;

    /** @var AttributeValuesExtractor */
    private $valuesExtractor;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ProductResource $productResource,
        FilterInterface $filter,
        AttributeValuesExtractor $valuesExtractor
    ) {
        $this->scopeConfig     = $scopeConfig;
        $this->productResource = $productResource;
        $this->filter          = $filter;
        $this->valuesExtractor  = $valuesExtractor;
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
        $attributes = (string) $this->scopeConfig->getValue(self::CONFIG_PATH, Scope::SCOPE_STORES, $storeId);
        return array_filter(array_map([$this->productResource, 'getAttribute'], explode(',', $attributes)));
    }
}
