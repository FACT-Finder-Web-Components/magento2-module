<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\StoreManagerInterface;

class Products implements \IteratorAggregate
{
    private ProductRepositoryInterface $productRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private StoreManagerInterface $storeManager;

    /** @var int */
    private $batchSize;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager,
        int $batchSize = 300
    ) {
        $this->productRepository     = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager          = $storeManager;
        $this->batchSize             = $batchSize;
    }

    /**
     * @return \Traversable|ProductInterface[]
     */
    public function getIterator()
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
            ->addFilter('entity_id', '1')
            ->addFilter('store_id', $this->storeManager->getStore()->getId())
            ->addFilter('visibility', Visibility::VISIBILITY_NOT_VISIBLE, 'neq')
            ->setPageSize($this->batchSize)
            ->setCurrentPage($page);
    }
}
