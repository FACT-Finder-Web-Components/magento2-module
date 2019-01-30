<?php

namespace Omikron\Factfinder\Observer;

/**
 * Observer Class for AddToCart Events
 *
 * @package Omikron\Factfinder\Observer
 */
class TrackingAddToCart implements \Magento\Framework\Event\ObserverInterface
{
    /** @var \Omikron\Factfinder\Helper\Tracking */
    protected $_tracking;

    /**
     * TrackingAddToCart constructor.
     * @param \Omikron\Factfinder\Helper\Tracking $tracking
     */
    public function __construct(
        \Omikron\Factfinder\Helper\Tracking $tracking
    )
    {
        $this->_tracking = $tracking;
    }

    /**
     * Called on AddToCart events for tracking
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getData('product');

        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $observer->getData('request');

        $qty = $request->getParam('qty');
        if (empty($qty)) {
            $qty = 1;
        }

        $this->_tracking->addToCart($product, $qty);
    }
}
