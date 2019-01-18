<?php

namespace Omikron\Factfinder\Observer;

/**
 * Observer Class for Checkout Event Tracking
 * @package Omikron\Factfinder\Observer
 */
class TrackingCheckout implements \Magento\Framework\Event\ObserverInterface
{

    /** @var \Omikron\Factfinder\Helper\Tracking */
    protected $_tracking;

    /**
     * TrackingCheckout constructor.
     * @param \Omikron\Factfinder\Helper\Tracking $tracking
     */
    public function __construct(
        \Omikron\Factfinder\Helper\Tracking $tracking
    )
    {
        $this->_tracking = $tracking;
    }

    /**
     * Called on checkout events for tracking
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');
        $this->_tracking->checkout($order);
    }
}
