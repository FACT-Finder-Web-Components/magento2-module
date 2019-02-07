<?php

declare(strict_types = 1);

namespace Omikron\Factfinder\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Omikron\Factfinder\Model\Consumer\Tracking\AddToCartTracking;

class TrackingAddToCart implements \Magento\Framework\Event\ObserverInterface
{
    /** @var AddToCartTracking */
    protected $tracking;

    public function __construct(AddToCartTracking $tracking)
    {
        $this->tracking = $tracking;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var ProductInterface $product */
        $product = $observer->getData('product');
        /** @var RequestInterface $request */
        $request = $observer->getData('request');

        $qty = $request->getParam('qty');
        if (!$qty) {
            $qty = 1;
        }

        $this->tracking->execute($product, $qty);
    }
}
