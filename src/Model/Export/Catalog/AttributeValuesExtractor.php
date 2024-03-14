<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog;

use DateTime;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Omikron\Factfinder\Api\Filter\FilterInterface;
use Omikron\Factfinder\Model\Formatter\NumberFormatter;
use UnexpectedValueException;

class AttributeValuesExtractor
{
    public function __construct(
        private readonly FilterInterface $filter,
        private readonly NumberFormatter $numberFormatter,
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @param Product   $product
     * @param Attribute $attribute
     *
     * @return array
     * @throws \Exception
     */
    //phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
    public function getAttributeValues(Product $product, Attribute $attribute): array
    {
        $code   = $attribute->getAttributeCode();
        $value  = $product->getDataUsingMethod($code) ?? $product->getData($code);
        $values = [];

        switch ($attribute->getFrontendInput()) {
            case 'boolean':
                $values[] = $value ? __('Yes') : __('No');
                break;
            case 'price':
                $values[] = $this->numberFormatter->format((float) $value);
                break;
            case 'select':
                $value = $product->getAttributeText($code);
                if (is_array($value)) {
                    $value = reset($value);
                }
                $values[] = (string) $value;
                break;
            case 'multiselect':
                $values = (array) $product->getAttributeText($code);
                break;
            case 'date':
                $values[] = $value ? (new DateTime($value))->format("Y-m-d'T'") : '';
                break;
            case 'datetime':
                $values[] = $value ? (new DateTime($value))->format("Y-m-d'T'H:i:sP") : '';
                break;
            default:
                if (!is_scalar($value)) {
                    switch (true) {
                        case $value === null:
                            $value = '';
                            break;
                        default:
                            $msg =
                                "Attribute '{$code}' could not be exported. Please consider writing your own field model";
                            throw new UnexpectedValueException($msg);
                    }
                }
                $values[] = (string) $value;
                break;
        }

        return array_filter(
            array_map(
                [
                    $this->filter,
                    'filterValue'
                ],
                $values
            )
        );
    }
}
