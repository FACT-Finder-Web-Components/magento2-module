<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer\Tracking;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\Data\TrackingProductInterface;
use Omikron\Factfinder\Api\Data\TrackingProductInterfaceFactory;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Helper\Product as ProductHelper;
use Omikron\Factfinder\Model\Api\Tracking;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AddToCartTest extends TestCase
{
    /** @var  MockObject|Tracking */
    private $trackingMock;

    /** @var MockObject|TrackingProductInterfaceFactory */
    private $trackingProductFactoryMock;

    /** @var MockObject|FieldRolesInterface */
    private $fieldRolesMock;

    /** @var MockObject|ProductHelper */
    private $productHelperMock;

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

    public function test_execute_track_event_successfully()
    {
        $this->requestMock->method('getParam')->with('qty')->willReturn(0);
        $this->productMock->method('getFinalPrice')->with(1)->willReturn(9.99);
        $this->productHelperMock->expects($this->exactly(2))
            ->method('get')->willReturnMap([
                ['id', $this->productMock, $this->storeMock, 1],
                ['sku', $this->productMock, $this->storeMock, 'product-sku-1'],
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

    protected function setUp()
    {
        $this->storeMock                  = $this->createMock(StoreInterface::class);
        $this->trackingMock               = $this->createMock(Tracking::class);
        $this->fieldRolesMock             = $this->createMock(FieldRolesInterface::class);
        $this->trackingProductFactoryMock = $this->createMock(TrackingProductInterfaceFactory::class);
        $this->productHelperMock          = $this->createMock(ProductHelper::class);
        $this->requestMock                = $this->createMock(RequestInterface::class);
        $this->productMock                = $this->createMock(Product::class);
        $this->observerMock               = $this->createMock(Observer::class);

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
            $this->productHelperMock,
            $this->fieldRolesMock,
            $this->createConfiguredMock(StoreManagerInterface::class, ['getStore' => $this->storeMock])
        );
    }
}
