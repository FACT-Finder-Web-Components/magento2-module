<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Traversable;

class Products implements \IteratorAggregate
{
    /**
     * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
     * phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine
     */
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly StoreManagerInterface $storeManager,
        private readonly int $batchSize = 300,
    ) {}

    /**
     * @return Traversable|ProductInterface[]
     */
    public function getIterator(): Traversable
    {
        $page = 1;
        while (true) {
            $query = $this->getQuery($page)->create();
            $list  = $this->productRepository->getList($query);
            yield from $list->getItems();
            if ($page * $this->batchSize >= $list->getTotalCount()) {
                break;
            }
            $page++;
        }
    }

    protected function getQuery(int $page): SearchCriteriaBuilder
    {
        return $this->searchCriteriaBuilder
            ->addFilter('status', Status::STATUS_ENABLED)
            ->addFilter('store_id', $this->storeManager->getStore()->getId())
            ->addFilter('visibility', Visibility::VISIBILITY_NOT_VISIBLE, 'neq')
            ->setPageSize($this->batchSize)
            ->setCurrentPage($page);
    }
}
