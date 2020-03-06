<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductType;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\LinkManagement;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProductType;
use Omikron\Factfinder\Api\Export\ExportEntityInterface;
use Omikron\Factfinder\Api\Filter\FilterInterface;
use Omikron\Factfinder\Model\Export\Catalog\Entity\ProductVariationFactory;
use Omikron\Factfinder\Model\Formatter\NumberFormatter;

class ConfigurableDataProvider extends SimpleDataProvider
{
    /** @var LinkManagement */
    private $linkManagement;

    /** @var ConfigurableProductType */
    private $productType;

    /** @var FilterInterface */
    private $filter;

    /** @var ProductVariationFactory */
    private $variationFactory;

    public function __construct(
        Product $product,
        NumberFormatter $numberFormatter,
        LinkManagement $linkManagement,
        ConfigurableProductType $productType,
        FilterInterface $filter,
        ProductVariationFactory $variationFactory,
        array $productFields = []
    ) {
        parent::__construct($product, $numberFormatter, $productFields);
        $this->linkManagement   = $linkManagement;
        $this->productType      = $productType;
        $this->filter           = $filter;
        $this->variationFactory = $variationFactory;
    }

    public function getEntities(): iterable
    {
        yield from parent::getEntities();
        yield from array_map($this->productVariation($this->product), $this->getChildren($this->product->getSku()));
    }

    public function toArray(): array
    {
        $data = ['HasVariants' => 1] + parent::toArray();

        $options = array_merge([], ...array_values($this->getOptions($this->product)));
        if ($options) {
            $data = ['Attributes' => ($data['Attributes'] ?? '|') . implode('|', $options) . '|'] + $data;
        }

        return $data;
    }

    private function productVariation(Product $product): callable
    {
        $options = $this->getOptions($product);
        $data    = parent::toArray();

        return function (Product $variation) use ($options, $product, $data): ExportEntityInterface {
            return $this->variationFactory->create([
                'product' => $variation,
                'configurable' => $product,
                'data'    => ['Attributes' => '|' . implode('|', $options[$variation->getSku()] ?? []) . '|'] + $data,
            ]);
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
     * @return ProductInterface[]
     */
    private function getChildren(string $sku): array
    {
        return $this->linkManagement->getChildren($sku);
    }
}
