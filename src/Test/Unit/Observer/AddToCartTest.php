<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer\Tracking;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\FactFinder\Communication\Credentials;
use Omikron\FactFinder\Communication\Resource\Builder;
use Omikron\FactFinder\Communication\Resource\Tracking\Product as TrackingProduct;
use Omikron\FactFinder\Communication\ResourceInterface;
use Omikron\Factfinder\Model\Api\CredentialsFactory;
use Omikron\Factfinder\Model\SessionData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AddToCartTest extends TestCase
{
    /** @var MockObject|FieldRolesInterface */
    private $fieldRolesMock;

    /** @var MockObject|Observer */
    private $observerMock;

    /** @var MockObject|RequestInterface */
    private $requestMock;

    /** @var MockObject|Product */
    private $productMock;

    /** @var MockObject|Builder */
    private $builderMock;

    /** @var MockObject|ResourceInterface */
    private $resourceMock;

    /** @var MockObject|CommunicationConfigInterface */
    private $configMock;

    /** @var AddToCart */
    private $addToCart;

    public function test_tracking_is_skipped_if_the_integration_is_disabled()
    {
        $this->configMock->method('isChannelEnabled')->willReturn(false);
        $this->observerMock->expects($this->never())->method('getData');
        $this->addToCart->execute($this->observerMock);
    }

    public function test_execute_track_event_successfully()
    {
        $qty                   = 2;
        $price                 = 9.99;
        $trackingProductNumber = '1';
        $sku                   = 'product-sku-1';

        $this->configMock->method('isChannelEnabled')->willReturn(true);
        $this->requestMock->method('getParam')->with('qty')->willReturn($qty);
        $this->productMock->method('getFinalPrice')->with(2)->willReturn($price);

        $this->fieldRolesMock->expects($this->exactly(2))->method('fieldRoleToAttribute')->willReturnMap(
            [
                [$this->productMock, 'trackingProductNumber', $trackingProductNumber],
                [$this->productMock, 'masterArticleNumber', $sku],
            ]);

        $expectedProduct = new TrackingProduct($trackingProductNumber, $sku, $price, $qty);
        $this->resourceMock->expects($this->once())->method('track')->with($this->anything(), $this->anything(), [$expectedProduct], $this->anything());
        $this->addToCart->execute($this->observerMock);
    }

    protected function setUp(): void
    {
        $this->fieldRolesMock = $this->createMock(FieldRolesInterface::class);
        $this->requestMock    = $this->createMock(RequestInterface::class);
        $this->productMock    = $this->createMock(Product::class);
        $this->observerMock   = $this->createMock(Observer::class);
        $this->configMock     = $this->createMock(CommunicationConfigInterface::class);
        $this->resourceMock   = $this->createConfiguredMock(ResourceInterface::class, ['track' => []]);
        $this->builderMock    = $this->createMock(Builder::class);
        $this->builderMock->method('withApiVersion')->willReturn($this->builderMock);
        $this->builderMock->method('withServerUrl')->willReturn($this->builderMock);
        $this->builderMock->method('withCredentials')->willReturn($this->builderMock);
        $this->builderMock->method('withLogger')->willReturn($this->builderMock);
        $this->builderMock->method('build')->willReturn($this->resourceMock);

        $this->fieldRolesMock->method('getFieldRole')->willReturnMap(
            [
                ['trackingProductNumber', null, 'id'],
                ['masterArticleNumber', null, 'sku'],
            ]);

        $this->observerMock->method('getData')->willReturnMap(
            [
                ['request', null, $this->requestMock],
                ['product', null, $this->productMock],
            ]);

        $this->addToCart = new AddToCart(
            $this->fieldRolesMock,
            $this->configMock,
            $this->createConfiguredMock(CredentialsFactory::class, ['create' => $this->createMock(Credentials::class)]),
            $this->createMock(SessionData::class),
            $this->builderMock,
            $this->createMock(LoggerInterface::class)
        );
    }
}
