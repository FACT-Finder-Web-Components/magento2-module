<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Catalog\Model\Category;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;

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

    public function getValue(): string
    {
        $path = $this->communicationConfig->getVersion() === CommunicationConfigInterface::NG_VERSION
            ? $this->ngPath($this->getCurrentCategory())
            : $this->standardPath($this->getCurrentCategory());

        return implode(',', $path);
    }

    private function standardPath(Category $category): array
    {
        $path  = 'ROOT';
        $value = $this->initial;
        foreach ($this->getCategoryPath($category) as $category) {
            $value[] = sprintf("filter{$this->param}%s=%s", $path, urlencode(trim($category)));
            $path    .= urlencode('/' . trim($category));
        }

        return $value;
    }

    private function ngPath(Category $category): array
    {
        $categoryPath = $this->getCategoryPath($category);
        return $this->initial + [sprintf('filter=%s', urlencode($this->param . ':' . implode('/', $categoryPath)))];
    }

    /**
     * @param Category $category
     *
     * @return string[]
     */
    private function getCategoryPath(Category $category): array
    {
        $categories = $category->getParentCategories();
        usort($categories, function (Category $a, Category $b): int {
            return $a->getLevel() - $b->getLevel();
        });
        return array_map([$this, 'getCategoryName'], $categories);
    }

    private function getCategoryName(Category $category): string
    {
        return (string) $category->getName();
    }

    private function getCurrentCategory(): Category
    {
        return $this->registry->registry('current_category');
    }
}
