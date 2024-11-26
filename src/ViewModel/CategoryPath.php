<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Catalog\Model\Category;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface as Scope;
use Omikron\Factfinder\Model\Config\CommunicationConfig;

class CategoryPath implements ArgumentInterface
{
    private const PATH_CATEGORY_PATH_NAME = 'factfinder/general/category_path_name';

    public function __construct(
        private readonly Registry             $registry,
        private readonly ScopeConfigInterface $scopeConfig,
    ) {
    }

    public function getCategoryPath(): array
    {
        $values = [];

        foreach ($this->getParentCategories($this->getCurrentCategory()) as $item) {
            $categoryName = trim($item->getName());
            $values[]     = $categoryName;
        }

        return $values;
    }

    public function getCategoryPathFieldName(): string
    {
        return $this->scopeConfig->getValue(self::PATH_CATEGORY_PATH_NAME, Scope::SCOPE_STORE) ?? 'CategoryPath';
    }

    /**
     * @param Category|null $category
     *
     * @return Category[]
     */
    private function getParentCategories(?Category $category): array
    {
        $categories = $category ? $category->getParentCategories() : [];
        usort($categories, fn (Category $a, Category $b): int => $a->getLevel() - $b->getLevel());

        return $categories;
    }

    private function getCurrentCategory(): ?Category
    {
        return $this->registry->registry('current_category');
    }
}
