<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Catalog\Api\Data\CategoryInterface as Category;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CategoryPath implements ArgumentInterface
{
    /** @var Registry */
    private $registry;

    /** @var string */
    private $param;

    /** @var string[] */
    private $initial;

    public function __construct(
        Registry $registry,
        string $param = 'CategoryPath',
        array $initial = []
    ) {
        $this->param    = $param;
        $this->registry = $registry;
        $this->initial  = $initial;
    }

    public function getValue(): string
    {
        $path  = 'ROOT';
        $value = $this->initial;
        foreach ($this->getParentCategories($this->getCurrentCategory()) as $item) {
            $value[] = sprintf("filter{$this->param}%s=%s", $path, urlencode($item->getName()));
            $path    .= urlencode('/' . $item->getName());
        }
        return implode(',', $value);
    }

    /**
     * @param Category $category
     *
     * @return Category[]
     */
    private function getParentCategories(Category $category): array
    {
        $categories = $category->getParentCategories();
        usort($categories, function (Category $a, Category $b): int {
            return $a->getLevel() - $b->getLevel();
        });
        return $categories;
    }

    private function getCurrentCategory(): Category
    {
        return $this->registry->registry('current_category');
    }
}
