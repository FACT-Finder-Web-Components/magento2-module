<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Catalog\Model\Category;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CategoryPath implements ArgumentInterface
{
    /** @var Registry */
    private $registry;

    /** @var string */
    private $param;

    public function __construct(Registry $registry, string $param = 'CategoryPath')
    {
        $this->param    = $param;
        $this->registry = $registry;
    }

    public function getValue(): string
    {
        $path  = 'ROOT';
        $value = [];
        foreach ($this->getCurrentCategory()->getParentCategories() as $item) {
            $value[] = sprintf("filter{$this->param}%s=%s", $path, urlencode($item->getName()));
            $path    .= urlencode('/' . $item->getName());
        }
        return implode(',', $value);
    }

    private function getCurrentCategory(): Category
    {
        return $this->registry->registry('current_category');
    }
}
