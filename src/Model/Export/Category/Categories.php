<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Category;

use IteratorAggregate;
use Magento\Catalog\Api\CategoryListInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class Categories implements IteratorAggregate
{
    private $searchCriteriaBuilder;
    private $storeManager;
    private $categoryList;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager,
        CategoryListInterface $categoryList
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager          = $storeManager;
        $this->categoryList          = $categoryList;
    }

    public function getIterator()
    {
        yield from $this->categoryList->getList($this->getCriteria()->create())->getItems();
    }

    private function getCriteria(): SearchCriteriaBuilder
    {
        return  $this->searchCriteriaBuilder;

        $inStores = [
            Store::DEFAULT_STORE_ID,
            (int) $this->storeManager->getStore()->getId()
        ];

        return $this->searchCriteriaBuilder->addFilter('store_id', $inStores, 'in');
    }
}
