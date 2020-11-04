<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer\Tracking;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Omikron\FactFinder\Communication\Resource\Tracking\Product;
use Omikron\Factfinder\Observer\BaseTracking;

class Checkout extends BaseTracking implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        if (!$this->communicationConfig->isChannelEnabled()) {
            return;
        }

        /** @var Quote $cart */
        $cart = $observer->getData('quote');

        $trackingProducts = array_map(function (Item $item) {
            return new Product(
                $this->getProductData('trackingProductNumber', $item->getProduct()),
                $this->getProductData('masterArticleNumber', $item->getProduct()),
                (float) $item->getPrice(),
                (int) $item->getQty()
            );
        }, $cart->getAllVisibleItems());

        $this->getTracking()->track(
            'checkout',
            $this->communicationConfig->getChannel(),
            $trackingProducts,
            $this->sessionData->getSessionId(),
            $this->sessionData->getUserId()
        );
    }
}
