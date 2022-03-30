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
    private ConfigurableProductType $productType;
    private FilterInterface $filter;
    private ProductVariationFactory $variationFactory;
    private ProductRepositoryInterface $productRepository;
    private SearchCriteriaBuilder $builder;

    public function __construct(
        Product $product,
        NumberFormatter $numberFormatter,
        ConfigurableProductType $productType,
        FilterInterface $filter,
        ProductVariationFactory $variationFactory,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $builder,
        array $productFields = []
    ) {
        parent::__construct($product, $numberFormatter, $productFields);
        $this->productType       = $productType;
        $this->filter            = $filter;
        $this->variationFactory  = $variationFactory;
        $this->productRepository = $productRepository;
        $this->builder           = $builder;
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
            $data['FilterAttributes'] = ($data['FilterAttributes'] ?: '|') . implode('|', array_unique($options)) . '|';
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
            ]);
        };
    }

    private function getOptions(Product $product): array
    {
        $sanitize = fn(string $phrase): string => $this->filter->filterValue($this->valueOrEmptyStr($phrase));

        return array_reduce($this->productType->getConfigurableOptions($product), function (array $res, array $option) use ($sanitize) {
            foreach ($option as ['sku' => $sku, 'super_attribute_label' => $label, 'option_title' => $value]) {
                $res[$sku][] = "{$sanitize($label)}={$sanitize($value)}";
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
        $childrenIds = $this->productType->getChildrenIds($product->getId());
        //if $childrenIds is empty the entity_id filter will thrown an SQL syntax error
        if (empty($childrenIds)) {
            return [];
        }
        return $this->productRepository
            ->getList($this->builder->addFilter('entity_id', $childrenIds, 'in')
            ->create())
            ->getItems();
    }

    private function valueOrEmptyStr($value): string
    {
        return is_string($value) ? $value : '';
    }
}
