<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\RequestInterface;
use Traversable;

#[AllowDynamicProperties]
class ExportPreviewProducts implements \IteratorAggregate
{
    /**
     * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
     * phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine
     */
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly RequestInterface $request,
    ) {
    }

    /**
     * @return Traversable|ProductInterface[]
     */
    public function getIterator(): Traversable
    {
        $entityId = $this->getEntityId();

        /** @var Product $list */
        $list  = $this->productRepository->getById($entityId);

        yield from [$list];
    }

    public function getEntityId(): int
    {
        if ($this->entityId === 0) {
            $this->entityId = (int) $this->request->getParam('entityId', 0);
        }

        return $this->entityId;
    }

    public function setEntityId(int $entityId): void
    {
        $this->entityId = $entityId;
    }
}
