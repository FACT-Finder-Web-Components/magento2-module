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
    private const CONFIG_PATH_ATTRIBUTES           = 'factfinder/data_transfer/ff_additional_attributes';
    private const CONFIG_PATH_ATTRIBUTES_NUMERICAL = 'factfinder/data_transfer/ff_additional_attributes_numerical';

    protected $numerical = false;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var ProductResource */
    private $productResource;

    /** @var FilterInterface */
    private $filter;

    /** @var AttributeValuesExtractor */
    private $valuesExtractor;

    private $cachedAttributes = [];

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
        foreach ($this->getAttributes($storeId, $this->numerical) as $attribute) {
            $label = $this->filter->filterValue($attribute->getStoreLabel($storeId));
            foreach ($this->valuesExtractor->getAttributeValues($product, $attribute) as $value) {
                $values .= "|{$label}={$value}";
            }
        }

        return $values ? "{$values}|" : '';
    }

    /**
     * @param int $storeId
     * @param bool $numerical
     *
     * @return Attribute[]
     */
    private function getAttributes(int $storeId, bool $numerical): array
    {
        if (!isset($this->cachedAttributes[$storeId][(int) $numerical])) {
            $configPath = $numerical ? self::CONFIG_PATH_ATTRIBUTES_NUMERICAL : self::CONFIG_PATH_ATTRIBUTES;
            $attributes = (string) $this->scopeConfig->getValue($configPath, Scope::SCOPE_STORES, $storeId);
            $this->cachedAttributes[$storeId][(int) $numerical] = array_filter(array_map([$this->productResource, 'getAttribute'], explode(',', $attributes)));
        }

        return $this->cachedAttributes[$storeId][(int) $numerical];
    }
}
