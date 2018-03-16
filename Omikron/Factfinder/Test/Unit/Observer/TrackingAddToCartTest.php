<?php

namespace Omikron\Factfinder\Test\Unit\Model\Source;

use \Omikron\Factfinder\Helper\Tracking;
use Omikron\Factfinder\Observer\TrackingAddToCart;
use \Magento\Framework\Event\Observer;
use \Magento\Catalog\Model\Product;
use \Magento\Framework\App\RequestInterface;

class TrackingAddToCartTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->method('getParam')
            ->with($this->equalTo('qty'))
            ->willReturn(null);

        $map = [
            ['product', null, $product],
            ['request', null, $request]
        ];

        $tracking = $this->getMockBuilder(Tracking::class)
            ->disableOriginalConstructor()
            ->getMock();
        $tracking->method('addToCart')
            ->withAnyParameters()
            ->willReturn(null);
        $observer = $this->getMockBuilder(Observer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $observer->expects($this->any())
            ->method('getData')
            ->will($this->returnValueMap($map));

        $trackingAddToPart = new TrackingAddToCart($tracking);

        $this->assertNull($trackingAddToPart->execute($observer));
    }
}
