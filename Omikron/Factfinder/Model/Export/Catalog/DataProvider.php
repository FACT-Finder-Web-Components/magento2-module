<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog;

use Magento\Catalog\Api\Data\ProductInterface;
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

    public function __construct(Products $products, ObjectManagerInterface $objectManager, array $entityTypes)
    {
        $this->products      = $products;
        $this->objectManager = $objectManager;
        $this->entityTypes   = $entityTypes;
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

    private function entitiesFrom(ProductInterface $product): DataProviderInterface
    {
        return $this->objectManager->create($this->entityTypes[$product->getTypeId()], ['product' => $product]); // phpcs:ignore
    }
}
