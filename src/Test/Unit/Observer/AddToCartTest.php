<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer\Tracking;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Store\Api\Data\StoreInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Api\Data\TrackingProductInterface;
use Omikron\Factfinder\Api\Data\TrackingProductInterfaceFactory;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Model\Api\Tracking;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AddToCartTest extends TestCase
{
    /** @var MockObject|Tracking */
    private $trackingMock;

    /** @var MockObject|TrackingProductInterfaceFactory */
    private $trackingProductFactoryMock;

    /** @var MockObject|FieldRolesInterface */
    private $fieldRolesMock;

    /** @var MockObject|Observer */
    private $observerMock;

    /** @var MockObject|RequestInterface */
    private $requestMock;

    /** @var MockObject|Product */
    private $productMock;

    /** @var MockObject|StoreInterface */
    private $storeMock;

    /** @var AddToCart */
    private $addToCart;

    /** @var MockObject|CommunicationConfigInterface */
    private $configMock;

    public function test_execute_track_event_successfully()
    {
        $this->configMock->method('isChannelEnabled')->willReturn(true);
        $this->requestMock->method('getParam')->with('qty')->willReturn(0);
        $this->productMock->method('getFinalPrice')->with(1)->willReturn(9.99);
        $this->fieldRolesMock->expects($this->exactly(2))
            ->method('fieldRoleToAttribute')->willReturnMap([
                [$this->productMock, 'trackingProductNumber', '1'],
                [$this->productMock, 'masterArticleNumber', 'product-sku-1'],
            ]);

        $this->trackingProductFactoryMock->expects($this->once())
            ->method('create')
            ->with([
                'trackingNumber'      => '1',
                'masterArticleNumber' => 'product-sku-1',
                'price'               => 9.99,
                'count'               => 1,
            ])->willReturn($this->createMock(TrackingProductInterface::class));

        $this->trackingMock->expects($this->once())->method('execute')
            ->with($this->isType('string'), $this->isInstanceOf(TrackingProductInterface::class));

        $this->addToCart->execute($this->observerMock);
    }

    public function test_no_tracking_if_integration_is_disabled()
    {
        $this->configMock->method('isChannelEnabled')->willReturn(false);
        $this->trackingMock->expects($this->any())->method('execute');
        $this->addToCart->execute($this->observerMock);
    }

    protected function setUp()
    {
        $this->storeMock      = $this->createMock(StoreInterface::class);
        $this->trackingMock   = $this->createMock(Tracking::class);
        $this->fieldRolesMock = $this->createMock(FieldRolesInterface::class);
        $this->requestMock    = $this->createMock(RequestInterface::class);
        $this->productMock    = $this->createMock(Product::class);
        $this->observerMock   = $this->createMock(Observer::class);
        $this->configMock     = $this->createMock(CommunicationConfigInterface::class);

        $this->trackingProductFactoryMock = $this->getMockBuilder(TrackingProductInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->fieldRolesMock->method('getFieldRole')->willReturnMap([
            ['trackingProductNumber', null, 'id'],
            ['masterArticleNumber', null, 'sku'],
        ]);

        $this->observerMock->method('getData')->willReturnMap([
            ['request', null, $this->requestMock],
            ['product', null, $this->productMock],
        ]);

        $this->addToCart = new AddToCart(
            $this->trackingMock,
            $this->trackingProductFactoryMock,
            $this->fieldRolesMock,
            $this->configMock
        );
    }
}
