<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Sales\Model\Order;
use Magento\Framework\Event\Observer;
use Omikron\Factfinder\Model\Consumer\Tracking\CreateOrder;

class TrackingCheckout implements \Magento\Framework\Event\ObserverInterface
{
    /** @var CreateOrder  */
    protected $createOrderTracking;

    public function __construct(CreateOrder $createOrder)
    {
        $this->createOrderTracking = $createOrder;
    }

    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getData('order');
        $this->createOrderTracking->execute($order);
    }
}
