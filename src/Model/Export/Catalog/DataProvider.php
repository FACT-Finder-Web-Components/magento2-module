<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Framework\ObjectManagerInterface;
use Omikron\Factfinder\Api\Export\DataProviderInterface;
use Omikron\Factfinder\Api\Export\ExportEntityInterface;
use Omikron\Factfinder\Api\Export\FieldInterface;

class DataProvider implements DataProviderInterface
{
    public function __construct(
        private readonly Products $products,
        private readonly ObjectManagerInterface $objectManager,
        private readonly array $fields,
        private readonly array $entityTypes
    ) {}

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

    private function entitiesFrom(ProductInterface $product): DataProviderInterface
    {
        $type = $this->entityTypes[$product->getTypeId()] ?? $this->entityTypes[ProductType::DEFAULT_TYPE];
        return $this->objectManager->create($type, ['product' => $product, 'productFields' => $this->productFields]); // phpcs:ignore
    }
}
