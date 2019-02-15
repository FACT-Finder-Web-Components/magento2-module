<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer\Tracking;

use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\Data\TrackingProductInterface;
use Omikron\Factfinder\Api\Data\TrackingProductInterfaceFactory;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Helper\Product as ProductHelper;
use Omikron\Factfinder\Model\Api\Tracking;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CheckoutTest extends TestCase
{
    /** @var  MockObject|Tracking */
    private $trackingMock;

    /** @var MockObject|TrackingProductInterfaceFactory */
    private $trackingProductFactoryMock;

    /** @var MockObject|FieldRolesInterface */
    private $fieldRolesMock;

    /** @var MockObject|ProductHelper */
    private $productHelperMock;

    /** @var MockObject|StoreManagerInterface */
    private $storeManagerMock;

    /** @var Checkout */
    private $checkoutObserver;

    public function test_execute_should_call_tracking_with_parameters_of_correct_type()
    {
        $itemOneMock = $this->createConfiguredMock(Item::class, ['getProduct' => $this->createMock(Product::class)]);
        $itemTwoMock = $this->createConfiguredMock(Item::class, ['getProduct' => $this->createMock(Product::class)]);
        $cartMock    = $this->createConfiguredMock(Quote::class, ['getAllVisibleItems' => [$itemOneMock, $itemTwoMock]]);
        $observerMock = $this->createMock(Observer::class);
        $observerMock->method('getData')->with('quote')->willReturn($cartMock);
        $storeMock = $this->createMock(StoreInterface::class);
        $this->storeManagerMock->method('getStore')->willReturn($storeMock);

        $this->trackingProductFactoryMock->expects($this->atLeastOnce())->method('create')->willReturn(
            $this->getMockBuilder(TrackingProductInterface::class)->getMock()
        );
        $this->trackingMock->expects($this->once())->method('execute')
            ->with($this->isType('string'), $this->isInstanceOf(TrackingProductInterface::class));

        $this->checkoutObserver->execute($observerMock);
    }

    protected function setUp()
    {
        $this->trackingMock               = $this->createMock(Tracking::class);
        $this->fieldRolesMock             = $this->createMock(FieldRolesInterface::class);
        $this->trackingProductFactoryMock = $this->createMock(TrackingProductInterfaceFactory::class);
        $this->productHelperMock          = $this->createMock(ProductHelper::class);
        $this->storeManagerMock           = $this->createMock(StoreManagerInterface::class);

        $this->checkoutObserver = new Checkout(
            $this->trackingMock,
            $this->trackingProductFactoryMock,
            $this->productHelperMock,
            $this->fieldRolesMock,
            $this->storeManagerMock
        );
    }
}
