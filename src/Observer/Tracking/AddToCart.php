<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer\Tracking;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Omikron\FactFinder\Communication\Resource\Tracking\Product as TrackingProduct;
use Omikron\Factfinder\Observer\BaseTracking;

class AddToCart extends BaseTracking implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        if (!$this->communicationConfig->isChannelEnabled()) {
            return;
        }

        $request = $observer->getData('request');
        $product = $observer->getData('product');
        $qty     = (int) ($request->getParam('qty') ?: 1);
        $this->getTracking()->track(' cart', $this->communicationConfig->getChannel(), [
            new TrackingProduct(
                $this->getProductData('trackingProductNumber', $product),
                $this->getProductData('masterArticleNumber', $product),
                $product->getFinalPrice($qty),
                $qty
            )], $this->sessionData->getSessionId(), $this->sessionData->getUserId());
    }
}
