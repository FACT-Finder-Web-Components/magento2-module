<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductType;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProductType;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Omikron\Factfinder\Api\Export\ExportEntityInterface;
use Omikron\Factfinder\Api\Filter\FilterInterface;
use Omikron\Factfinder\Model\Export\Catalog\Entity\ProductVariationFactory;
use Omikron\Factfinder\Model\Formatter\NumberFormatter;

class ConfigurableDataProvider extends SimpleDataProvider
{
    /** @var ConfigurableProductType */
    private $productType;

    /** @var FilterInterface */
    private $filter;

    /** @var ProductVariationFactory */
    private $variationFactory;

    /** @var ProductRepositoryInterface  */
    private $productRepository;

    /** @var SearchCriteriaBuilder  */
    private $builder;

    public function __construct(
        Product $product,
        NumberFormatter $numberFormatter,
        ConfigurableProductType $productType,
        FilterInterface $filter,
        ProductVariationFactory $variationFactory,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $builder,
        array $productFields = [],
        array $variantFields = []
    ) {
        parent::__construct($product, $numberFormatter, $productFields);
        $this->productType      = $productType;
        $this->filter           = $filter;
        $this->variationFactory = $variationFactory;
        $this->productRepository = $productRepository;
        $this->builder           = $builder;
        $this->variantFields     = $variantFields;
    }

    public function getEntities(): iterable
    {
        yield from parent::getEntities();
        yield from array_map($this->productVariation($this->product), $this->getChildren($this->product));
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
            $sku = $variation->getSku();
            return $this->variationFactory->create([
               'product'      => $variation,
               'configurable' => $product,
               'data'         => ['FilterAttributes' => '|' . implode('|', $options[$sku] ?? []) . '|'] + $data,
               'fields'       => $this->variantFields
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
     * @param Product $product
     *
     * @return ProductInterface[]
     */
    private function getChildren(Product $product): array
    {
        return $this->productRepository
            ->getList($this->builder->addFilter('entity_id', $this->productType->getChildrenIds($this->product->getId()), 'in')
            ->create())
            ->getItems();
    }
}
