<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Framework\ObjectManagerInterface;
use Omikron\Factfinder\Api\Export\DataProviderInterface;
use Omikron\Factfinder\Api\Export\ExportEntityInterface;

class DataProvider implements DataProviderInterface
{
    public function __construct(
        private readonly Products $products,
        private readonly ObjectManagerInterface $objectManager,
        private readonly array $fields,
        private readonly array $entityTypes,
    ) {
    }

    /**
     * @return ExportEntityInterface[]
     */
    public function getEntities(): iterable
    {
        yield from []; // init generator: Prevent errors in case of an empty product collection
        foreach ($this->products as $product) {
            yield from $this->entitiesFrom($product)->getEntities();
        }
    }

    /**
     * @return ExportEntityInterface[]
     */
    public function getEntitiesBatch(int $offset, int $limit): iterable
    {
        yield from []; // init generator

        $productsBatch = $this->getProductsSlice($offset, $limit);

        foreach ($productsBatch as $product) {
            yield from $this->entitiesFrom($product)->getEntities();
        }
    }

    private function getProductsSlice(int $offset, int $limit): iterable
    {
        if (method_exists($this->products, 'getBatch')) {
            return $this->products->getBatch($offset, $limit);
        }

        if (method_exists($this->products, 'setPageSize') && method_exists($this->products, 'setCurPage')) {
            $pageNumber = (int) floor($offset / $limit) + 1;
            $this->products->setPageSize($limit);
            $this->products->setCurPage($pageNumber);

            return $this->products;
        }

        $productsArray = is_array($this->products)
            ? $this->products
            : iterator_to_array($this->products, false);

        return array_slice($productsArray, $offset, $limit);
    }

    private function entitiesFrom(ProductInterface $product): DataProviderInterface
    {
        $type = $this->entityTypes[$product->getTypeId()] ?? $this->entityTypes[ProductType::DEFAULT_TYPE];
        return $this->objectManager->create($type, ['product' => $product, 'productFields' => $this->fields]); // phpcs:ignore
    }
}
