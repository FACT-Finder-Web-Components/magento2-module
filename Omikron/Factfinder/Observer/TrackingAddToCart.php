<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Catalog\Api\Data\ProductInterface;
use Omikron\Factfinder\Model\Consumer\Tracking\AddToCart;

class TrackingAddToCart implements \Magento\Framework\Event\ObserverInterface
{
    /** @var AddToCart */
    protected $addToCartTracking;

    public function __construct(AddToCart $addToCart)
    {
        $this->addToCartTracking = $addToCart;
    }

    public function execute(Observer $observer)
    {
        /** @var ProductInterface $product */
        $product = $observer->getData('product');
        /** @var RequestInterface $request */
        $request = $observer->getData('request');

        $qty = (int) $request->getParam('qty');
        if (!$qty) {
            $qty = 1;
        }

        $this->addToCartTracking->execute($product, $qty);
    }
}
