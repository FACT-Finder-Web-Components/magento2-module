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
    private Registry $registry;
    private CommunicationConfig $communicationConfig;
    private string $param;
    private array $initial;

    public function __construct(
        Registry            $registry,
        CommunicationConfig $communicationConfig,
        string              $param = 'CategoryPath',
        array               $initial = []
    ) {
        $this->param               = $param;
        $this->registry            = $registry;
        $this->communicationConfig = $communicationConfig;
        $this->initial             = $initial;
    }

    public function __toString()
    {
        return $this->communicationConfig->getVersion() === Version::NG ? $this->getCategoryPath(
        ) : $this->getAddParams();
    }

    public function getCategoryPath(): string
    {
        if ($this->communicationConfig->getVersion() === Version::NG) {
            return implode(',', $this->ngPath($this->getCurrentCategory()));
        }

        return '';
    }

    public function getAddParams(): string
    {
        if ($this->communicationConfig->getVersion() === Version::NG) {
            return '';
        }

        return implode(',', $this->standardPath($this->getCurrentCategory()));
    }

    public function getCategoryPathFieldName(): string
    {
        return $this->param;
    }

    private function standardPath(?Category $category): array
    {
        $path  = 'ROOT';
        $value = $this->initial;
        foreach ($this->getParentCategories($category) as $item) {
            $categoryName = trim($item->getName());
            $value[]      = sprintf("filter{$this->param}%s=%s", $path, urlencode($categoryName));
            $path         .= urlencode('/' . $this->encodeCategoryName($categoryName));
        }

        return $value;
    }

    private function ngPath(?Category $category): array
    {
        $path = array_map(
            fn (Category $item): string => (string) $this->encodeCategoryName(trim($item->getName())),
            $category ? $this->getParentCategories($category) : []
        );

        return [sprintf('filter=%s', urlencode($this->param . ':' . implode('/', $path)))];
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

    private function encodeCategoryName(string $path): string
    {
        //important! do not override this method
        return preg_replace('/\+/', '%2B',
            preg_replace('/\//', '%2F', preg_replace('/%/', '%25', $path)));
    }
}
