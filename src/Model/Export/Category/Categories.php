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
    /**
     * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
     * phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine
     */
    public function __construct(
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly StoreManagerInterface $storeManager,
        private readonly CategoryListInterface $categoryList,
    ) {
    }

    /**
     * @return Traversable|CategoryInterface[]
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
