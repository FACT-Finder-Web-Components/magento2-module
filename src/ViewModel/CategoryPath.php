<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Catalog\Model\Category;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Omikron\FactFinder\Communication\Version;
use Omikron\Factfinder\Model\Config\CommunicationConfig;

class CategoryPath implements ArgumentInterface
{
    /** @var Registry */
    private $registry;

    /** @var CommunicationConfig */
    private $communicationConfig;

    /** @var string */
    private $param;

    /** @var string[] */
    private $initial;

    public function __construct(
        Registry $registry,
        CommunicationConfig $communicationConfig,
        string $param = 'CategoryPath',
        array $initial = []
    )
    {
        $this->param = $param;
        $this->registry = $registry;
        $this->communicationConfig = $communicationConfig;
        $this->initial = $initial;
    }

    public function getCategoryPath(): string
    {
        if ($this->communicationConfig->getVersion() === Version::NG) return implode(',', $this->ngPath($this->getCurrentCategory()));

        return '';
    }

    public function getAddParams(): string
    {
        if ($this->communicationConfig->getVersion() === Version::NG) return '';

        return implode(',', $this->standardPath($this->getCurrentCategory()));
    }

    public function getCategoryPathFieldName(): string
    {
        return $this->param;
    }

    private function standardPath(?Category $category): array
    {
        $path = 'ROOT';
        $value = $this->initial;
        foreach ($this->getParentCategories($category) as $item) {
            $value[] = sprintf("filter{$this->param}%s=%s", $path, urlencode(trim($item->getName())));
            $path .= urlencode('/' . trim($item->getName()));
        }

        return $value;
    }

    private function ngPath(?Category $category): array
    {
        $path = implode('/', $this->createPathFromCategory($category));
        return [sprintf('filter=%s', urlencode($this->param . ':' . $path))];
    }

    private function createPathFromCategory(?Category $category): array
    {
        return array_map(function (Category $item): string {
            return (string) $item->getName();
        }, $category ? $category->getParentCategories() : []
        );
    }

    /**
     * @param Category|null $category
     *
     * @return Category[]
     */
    private function getParentCategories(?Category $category): array
    {
        $categories = $category ? $category->getParentCategories() : [];
        usort($categories, function (Category $a, Category $b): int {
            return $a->getLevel() - $b->getLevel();
        }
        );

        return $categories;
    }

    private function getCurrentCategory(): ?Category
    {
        return $this->registry->registry('current_category');
    }
}
