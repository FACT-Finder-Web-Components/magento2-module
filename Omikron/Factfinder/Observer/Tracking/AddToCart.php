<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer\Tracking;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Omikron\Factfinder\Observer\BaseTracking;

class AddToCart extends BaseTracking implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $request = $observer->getRequest();
        $product = $observer->getProduct();
        $qty     = (int) ($request->getParam('qty') ?: 1);

        $this->tracking->execute('cart', $this->trackingProductFactory->create([
            'trackingNumber'      => $this->getProductData('trackingProductNumber', $product),
            'masterArticleNumber' => $this->getProductData('masterArticleNumber', $product),
            'price'               => $product->getFinalPrice($qty),
            'count'               => $qty,
        ]));
    }
}
