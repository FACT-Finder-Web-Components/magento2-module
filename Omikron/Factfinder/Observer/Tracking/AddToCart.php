<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer\Tracking;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddToCart extends BaseTracking implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $request = $observer->getRequest();
        $product = $observer->getProduct();
        $store   = $this->storeManager->getStore();
        $qty     = (int) $request->getParam('qty') ?? 1;

        $trackingProduct = $this->trackingProductFactory->create([
            'trackingNumber'        => $this->productHelper->get($this->fieldRoles->getFieldRole('trackingProductNumber'), $product, $store),
            'masterArticleNumber'   => $this->productHelper->get($this->fieldRoles->getFieldRole('masterArticleNumber'), $product, $store),
            'price'                 => $product->getFinalPrice($qty),
            'count'                 => $qty
        ]);

        $this->tracking->execute('cart', $trackingProduct);
    }
}
