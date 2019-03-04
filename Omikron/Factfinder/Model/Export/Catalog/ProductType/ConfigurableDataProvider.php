<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductType;

use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\LinkManagement;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProductType;
use Omikron\Factfinder\Api\Export\ExportEntityInterface;
use Omikron\Factfinder\Api\Filter\FilterInterface;
use Omikron\Factfinder\Model\Formatter\NumberFormatter;

class ConfigurableDataProvider extends SimpleDataProvider
{
    /** @var LinkManagement */
    private $linkManagement;

    /** @var ConfigurableProductType */
    private $productType;

    /** @var FilterInterface */
    private $filter;

    public function __construct(
        Product $product,
        NumberFormatter $numberFormatter,
        LinkManagement $linkManagement,
        ConfigurableProductType $productType,
        FilterInterface $filter,
        array $productFields = []
    ) {
        parent::__construct($product, $numberFormatter, $productFields);
        $this->linkManagement = $linkManagement;
        $this->productType    = $productType;
        $this->filter         = $filter;
    }

    public function getEntities(): iterable
    {
        yield from parent::getEntities();
        yield from array_map($this->childEntity($this->product), $this->getChildren($this->product->getSku()));
    }

    private function childEntity(Product $product): callable
    {
        $options = $this->getOptions($product);
        return function (Product $option) use ($options): ExportEntityInterface {
            return new class($option, $this->product, $this->numberFormatter, $options) implements ExportEntityInterface
            {
                /** @var Product */
                private $product;

                /** @var Product */
                private $parent;

                /** @var NumberFormatter */
                private $numberFormatter;

                /** @var array */
                private $attributes;

                public function __construct(
                    Product $product,
                    Product $parent,
                    NumberFormatter $numberFormatter,
                    array $attributes
                ) {
                    $this->product         = $product;
                    $this->parent          = $parent;
                    $this->numberFormatter = $numberFormatter;
                    $this->attributes      = $attributes;
                }

                public function getId(): int
                {
                    return $this->product->getId();
                }

                public function toArray(): array
                {
                    $attributes = implode('|', $this->attributes[$this->product->getSku()] ?? []);
                    return [
                        'ProductNumber' => (string) $this->product->getSku(),
                        'Master'        => (string) $this->parent->getSku(),
                        'Price'         => $this->numberFormatter->format((float) $this->product->getFinalPrice()),
                        'Availability'  => (int) $this->product->isAvailable(),
                        'Attributes'    => $attributes ? "|{$attributes}|" : '',
                    ];
                }
            };
        };
    }

    private function getOptions(Product $product): array
    {
        return array_reduce($this->productType->getConfigurableOptions($product), function (array $res, array $option) {
            foreach ($option as ['sku' => $sku, 'super_attribute_label' => $label, 'option_title' => $value]) {
                $res[$sku][] = "{$this->filter->filterValue($label)}={$this->filter->filterValue($value)}";
            }
            return $res;
        }, []);
    }

    /**
     * @param string $sku
     *
     * @return Product[]
     */
    private function getChildren(string $sku): array
    {
        return $this->linkManagement->getChildren($sku);
    }
}
