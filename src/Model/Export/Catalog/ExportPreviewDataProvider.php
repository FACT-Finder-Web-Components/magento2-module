<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Framework\ObjectManagerInterface;
use Omikron\Factfinder\Api\Export\DataProviderInterface;
use Omikron\Factfinder\Api\Export\ExportEntityInterface;
use Omikron\Factfinder\Api\Export\FieldInterface;

class ExportPreviewDataProvider implements DataProviderInterface
{
    /** @var ExportPreviewProducts */
    private $products;

    /** @var ObjectManagerInterface */
    private $objectManager;

    /** @var string[] */
    private $entityTypes;

    /** @var FieldInterface[] */
    private $productFields;

    /** @var array */
    private $data;

    public function __construct(
        ExportPreviewProducts $products,
        ObjectManagerInterface $objectManager,
        array $fields,
        array $entityTypes,
        array $data
    ) {
        $this->products      = $products;
        $this->objectManager = $objectManager;
        $this->entityTypes   = $entityTypes;
        $this->productFields = $fields;
        $this->data = $data;
    }

    /**
     * @return ExportEntityInterface[]
     */
    public function getEntities(): iterable
    {
        $this->products->setEntityId((int) $this->data['entityId'] ?? 0);
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
