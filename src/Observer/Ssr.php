<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;

class Ssr implements ObserverInterface
{
    /**
     * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
     * phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly array $useForHandles = ['factfinder_result_index', 'factfinder_category_view'],
    ) {
    }

    public function execute(Observer $observer)
    {
        if (!$this->scopeConfig->isSetFlag('factfinder/general/use_ssr', ScopeInterface::SCOPE_STORES)) {
            return;
        }

        $update = $observer->getData('layout')->getUpdate();
        if (array_intersect($this->useForHandles, $update->getHandles())) {
            $update->addHandle('factfinder_ssr');
        }
    }
}
