<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Omikron\Factfinder\Api\Filter\FilterInterface;
use Omikron\Factfinder\Model\Formatter\NumberFormatter;
use UnexpectedValueException;

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
        $code   = $attribute->getAttributeCode();
        $value  = $product->getDataUsingMethod($code);
        $values = [];

        switch ($attribute->getFrontendInput()) {
            case 'boolean':
                $values[] = $value ? __('Yes') : __('No');
                break;
            case 'price':
                $values[] = $this->numberFormatter->format((float) $value);
                break;
            case 'select':
                $values[] = (string) $product->getAttributeText($code);
                break;
            case 'multiselect':
                $values = (array) $product->getAttributeText($code);
                break;
            default:
                if (!is_scalar($value)) {
                    $msg = "Attribute '{$code}' could not be exported. Please consider writing your own field model";
                    throw new UnexpectedValueException($msg);
                }
                $values[] = (string) $value;
                break;
        }

        return array_filter(array_map([$this->filter, 'filterValue'], $values));
    }
}
