<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Catalog\Model\Category;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Omikron\Factfinder\Model\Config\FeatureConfig;

class CategoryView implements ObserverInterface
{
    private Registry $registry;
    private FeatureConfig $config;

    public function __construct(Registry $registry, FeatureConfig $config)
    {
        $this->registry = $registry;
        $this->config   = $config;
    }

    public function execute(Observer $observer)
    {
        if ($observer->getData('full_action_name') !== 'catalog_category_view') {
            return;
        }

        /** @var ?Category $category */
        $category = $this->registry->registry('current_category');
        if ($category && $category->getDisplayMode() !== Category::DM_PAGE && $this->config->useForCategories()) {
            $observer->getData('layout')->getUpdate()->addHandle('factfinder_category_view');
        }
    }
}
