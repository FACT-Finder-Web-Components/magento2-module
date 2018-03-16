<?php

namespace Omikron\Factfinder\Test\Unit\Model\Source;

use \Magento\Framework\Event;
use \Magento\Framework\Event\Observer;
use \Magento\Framework\DataObject;
use Omikron\Factfinder\Observer\TrackingCheckout;
use Omikron\Factfinder\Helper\Tracking;

class TrackingCheckoutTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $tracking = $this->getMockBuilder(Tracking::class)
            ->disableOriginalConstructor()
            ->getMock();
        $tracking->method('checkout')
            ->withAnyParameters()
            ->willReturn(null);
        $dataObject = $this->getMockBuilder(DataObject::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event = $this->getMockBuilder(Event::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event->method('getData')
            ->with($this->equalTo('order'))
            ->willReturn($dataObject);
        $observer = $this->getMockBuilder(Observer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $observer->method('getEvent')
            ->willReturn($event);

        $trackingCheckout = new TrackingCheckout($tracking);

        $this->assertNull($trackingCheckout->execute($observer));
    }
}
