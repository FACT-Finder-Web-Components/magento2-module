<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Catalog\Model\Category;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\FactFinder\Communication\Version;

class CategoryPath implements ArgumentInterface
{
    /** @var Registry */
    private $registry;

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    /** @var string */
    private $param;

    /** @var string[] */
    private $initial;

    public function __construct(
        Registry $registry,
        CommunicationConfigInterface $communicationConfig,
        string $param = 'CategoryPath',
        array $initial = []
    ) {
        $this->param               = $param;
        $this->registry            = $registry;
        $this->communicationConfig = $communicationConfig;
        $this->initial             = $initial;
    }

    public function __toString(): string
    {
        $path = $this->communicationConfig->getVersion() === Version::NG
            ? $this->ngPath($this->getCurrentCategory())
            : $this->standardPath($this->getCurrentCategory());

        return implode(',', $path);
    }

    private function standardPath(?Category $category): array
    {
        $path  = 'ROOT';
        $value = $this->initial;
        foreach ($this->getParentCategories($category) as $item) {
            $value[] = sprintf("filter{$this->param}%s=%s", $path, urlencode(trim($item->getName())));
            $path    .= urlencode('/' . trim($item->getName()));
        }

        return $value;
    }

    private function ngPath(?Category $category): array
    {
        $path = implode('/', $this->getCategoryPath($category));
        return $this->initial + [sprintf('filter=%s', urlencode($this->param . ':' . $path))];
    }

    private function getCategoryPath(?Category $category): array
    {
        return array_map(function (Category $item): string {
            return (string) $item->getName();
        }, $category ? $category->getParentCategories() : []);
    }

    public function getValue(): string
    {
        return $this;
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
        });

        return $categories;
    }

    private function getCurrentCategory(): ?Category
    {
        return $this->registry->registry('current_category');
    }
}
