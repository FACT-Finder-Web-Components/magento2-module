<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Category;

use IteratorAggregate;
use Magento\Catalog\Api\CategoryListInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Traversable;

class Categories implements IteratorAggregate
{
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private StoreManagerInterface $storeManager;
    private CategoryListInterface $categoryList;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager,
        CategoryListInterface $categoryList
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager          = $storeManager;
        $this->categoryList          = $categoryList;
    }

    /**
     * @return \Traversable|CategoryInterface[]
     */
    public function getIterator(): Traversable
    {
        yield from $this->categoryList->getList($this->getCriteria()->create())->getItems();
    }

    private function getCriteria(): SearchCriteriaBuilder
    {
        return $this->searchCriteriaBuilder
            ->addFilter(
                'entity_id',
                [
                    Category::TREE_ROOT_ID,
                    $this->storeManager->getStore()->getRootCategoryId()
                ],
                'nin'
            );
    }
}
