<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;

class Ssr implements ObserverInterface
{
    private ScopeConfigInterface $scopeConfig;

    /** @var string[]  */
    private array $useForHandles;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        array $useForHandles = ['factfinder_result_index', 'factfinder_category_view']
    ) {
        $this->scopeConfig   = $scopeConfig;
        $this->useForHandles = $useForHandles;
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
