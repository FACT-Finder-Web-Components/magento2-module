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
use Omikron\Factfinder\Model\Formatter\NumberFormatter;

class Attributes implements ProductFieldInterface
{
    private const CONFIG_PATH = 'factfinder/data_transfer/ff_additional_attributes';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var ProductResource */
    private $productResource;

    /** @var FilterInterface */
    private $filter;

    /** @var NumberFormatter */
    private $numberFormatter;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ProductResource $productResource,
        FilterInterface $filter,
        NumberFormatter $numberFormatter
    ) {
        $this->scopeConfig     = $scopeConfig;
        $this->productResource = $productResource;
        $this->filter          = $filter;
        $this->numberFormatter = $numberFormatter;
    }

    public function getValue(Product $product): string
    {
        $storeId = (int) $product->getStoreId();
        $values  = '';
        foreach ($this->getAttributes($storeId) as $attribute) {
            $label = $this->filter->filterValue($attribute->getStoreLabel($storeId));
            foreach ($this->getAttributeValues($product, $attribute) as $value) {
                $values .= "|{$label}={$value}";
            }
        }
        return $values ? "{$values}|" : '';
    }

    private function getAttributeValues(Product $product, Attribute $attribute): array
    {
        $value  = $product->getDataUsingMethod($attribute->getAttributeCode());
        $values = [];

        switch ($attribute->getFrontendInput()) {
            case 'boolean':
                $values[] = $value ? __('Yes') : __('No');
                break;
            case 'price':
                $values[] = $this->numberFormatter->format((float) $value);
                break;
            case 'select':
                $values[] = (string) $product->getAttributeText($attribute->getAttributeCode());
                break;
            case 'multiselect':
                $values = (array) $product->getAttributeText($attribute->getAttributeCode());
                break;
            default:
                $values[] = (string) $value;
                break;
        }

        return array_filter(array_map([$this->filter, 'filterValue'], $values));
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
