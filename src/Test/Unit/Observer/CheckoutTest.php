<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer\Tracking;

use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\Data\TrackingProductInterface;
use Omikron\Factfinder\Api\Data\TrackingProductInterfaceFactory;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Model\Api\Tracking;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CheckoutTest extends TestCase
{
    /** @var MockObject|Tracking */
    private $trackingMock;

    /** @var MockObject|TrackingProductInterfaceFactory */
    private $trackingProductFactoryMock;

    /** @var Checkout */
    private $checkoutObserver;

    public function test_execute_should_call_tracking_with_parameters_of_correct_type()
    {
        $quoteMock = $this->createConfiguredMock(Quote::class, ['getAllVisibleItems' => [
            $this->createConfiguredMock(Item::class, ['getProduct' => $this->createMock(Product::class)]),
            $this->createConfiguredMock(Item::class, ['getProduct' => $this->createMock(Product::class)]),
        ]]);

        $this->trackingProductFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->getMockBuilder(TrackingProductInterface::class)->getMock());

        $this->trackingMock->expects($this->once())
            ->method('execute')
            ->with($this->isType('string'), $this->isInstanceOf(TrackingProductInterface::class));

        $this->checkoutObserver->execute(new Observer(['quote' => $quoteMock]));
    }

    protected function setUp()
    {
        $this->trackingMock = $this->createMock(Tracking::class);

        $this->trackingProductFactoryMock = $this->getMockBuilder(TrackingProductInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->checkoutObserver = new Checkout(
            $this->trackingMock,
            $this->trackingProductFactoryMock,
            $this->createMock(FieldRolesInterface::class),
            $this->createMock(StoreManagerInterface::class)
        );
    }
}
