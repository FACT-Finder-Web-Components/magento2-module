<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer\Tracking;

use Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;

class Checkout extends BaseTracking implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        /** @var Quote $cart */
        $cart = $observer->getEvent()->getData('quote');
        $store = $this->storeManager->getStore();

        $trackingProducts = array_map(function (Item $item) use ($store) {
            return $this->trackingProductFactory->create(
                [
                    'trackingNumber'        => $this->productHelper->get($this->fieldRoles->getFieldRole('trackingProductNumber'), $item->getProduct(), $store),
                    'masterArticleNumber'   => $this->productHelper->get($this->fieldRoles->getFieldRole('masterArticleNumber'), $item->getProduct(), $store),
                    'price'                 => $item->getPrice(),
                    'count'                 => $item->getQty()
                ]
            );
        }, $cart->getAllVisibleItems());

        $this->tracking->execute('checkout', ...$trackingProducts);
    }
}
