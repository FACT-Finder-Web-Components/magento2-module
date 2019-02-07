<?php

namespace Omikron\Factfinder\Observer;
use Magento\Sales\Api\Data\OrderInterface;
use Omikron\Factfinder\Model\Consumer\Tracking\OrderTracking;

class TrackingCheckout implements \Magento\Framework\Event\ObserverInterface
{
    /** @var OrderTracking  */
    protected $tracking;

    public function __construct(
        OrderTracking $tracking
    ) {
        $this->tracking = $tracking;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var OrderInterface $order */
        $order = $observer->getEvent()->getData('order');
        $this->tracking->execute($order);
    }
}
