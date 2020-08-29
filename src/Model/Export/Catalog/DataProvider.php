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
    /** @var Products */
    private $products;

    /** @var ObjectManagerInterface */
    private $objectManager;

    /** @var string[] */
    private $entityTypes;

    /** @var ProductFieldProvider */
    private $fieldProvider;

    public function __construct(
        Products $products,
        ObjectManagerInterface $objectManager,
        ProductFieldProvider $fieldProvider,
        array $entityTypes
    ) {
        $this->products      = $products;
        $this->objectManager = $objectManager;
        $this->fieldProvider = $fieldProvider;
        $this->entityTypes   = $entityTypes;
    }

    /**
     * @return ExportEntityInterface[]
     */
    public function getEntities(): iterable
    {
        $fields = $this->fieldProvider->getFields();

        yield from []; // init generator: Prevent errors in case of an empty product collection
        foreach ($this->products as $product) {
            yield from $this->entitiesFrom($product, $fields)->getEntities();
        }
    }

    private function entitiesFrom(ProductInterface $product, array $fields): DataProviderInterface
    {
        $type = $this->entityTypes[$product->getTypeId()] ?? $this->entityTypes[ProductType::DEFAULT_TYPE];
        return $this->objectManager->create($type, ['product' => $product, 'productFields' => $fields]); // phpcs:ignore
    }
}
