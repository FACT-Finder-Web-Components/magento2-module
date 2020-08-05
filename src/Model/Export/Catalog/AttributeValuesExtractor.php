<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Omikron\Factfinder\Api\Filter\FilterInterface;
use Omikron\Factfinder\Model\Formatter\NumberFormatter;

class AttributeValuesExtractor
{
    /** @var FilterInterface */
    private $filter;

    /** @var NumberFormatter */
    private $numberFormatter;

    public function __construct(FilterInterface $filter, NumberFormatter $numberFormatter)
    {
        $this->filter          = $filter;
        $this->numberFormatter = $numberFormatter;
    }

    public function getAttributeValues(Product $product, Attribute $attribute): array
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
}
