<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer\Tracking;

use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
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

    /** @var MockObject|CommunicationConfigInterface */
    private $configMock;

    /** @var Checkout */
    private $checkoutObserver;

    public function test_execute_should_call_tracking_with_parameters_of_correct_type()
    {
        $this->configMock->method('isChannelEnabled')->willReturn(true);
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

    public function test_no_tracking_if_integration_is_disabled()
    {
        $this->configMock->method('isChannelEnabled')->willReturn(false);
        $this->trackingMock->expects($this->any())->method('execute');
        $this->checkoutObserver->execute(new Observer(['quote' => $this->createMock(Quote::class)]));
    }

    protected function setUp()
    {
        $this->trackingMock = $this->createMock(Tracking::class);
        $this->configMock = $this->createMock(CommunicationConfigInterface::class);
        $this->trackingProductFactoryMock = $this->getMockBuilder(TrackingProductInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->checkoutObserver = new Checkout(
            $this->trackingMock,
            $this->trackingProductFactoryMock,
            $this->createMock(FieldRolesInterface::class),
            $this->configMock
        );
    }
}
