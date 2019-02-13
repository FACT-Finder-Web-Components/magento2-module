<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer\Tracking;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Omikron\Factfinder\Observer\BaseTracking;

class Checkout extends BaseTracking implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        /** @var Quote $cart */
        $cart = $observer->getEvent()->getData('quote');

        $trackingProducts = array_map(function (Item $item) {
            return $this->trackingProductFactory->create([
                'trackingNumber'      => $this->getProductData('trackingProductNumber', $item->getProduct()),
                'masterArticleNumber' => $this->getProductData('masterArticleNumber', $item->getProduct()),
                'price'               => $item->getPrice(),
                'count'               => $item->getQty(),
            ]);
        }, $cart->getAllVisibleItems());

        $this->tracking->execute('checkout', ...$trackingProducts);
    }
}
