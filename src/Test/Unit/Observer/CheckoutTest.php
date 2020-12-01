<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer\Tracking;

use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
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

class CheckoutTest extends TestCase
{
    /** @var MockObject|Builder */
    private $builderMock;

    /** @var MockObject|ResourceInterface */
    private $resourceMock;

    /** @var MockObject|CommunicationConfigInterface */
    private $configMock;

    /** @var Checkout */
    private $checkoutObserver;

    public function test_execute_should_call_tracking_with_parameters_of_correct_type()
    {
        $qty                   = 1;
        $price                 = 9.99;
        $trackingProductNumber = '1';
        $sku                   = 'product-sku-1';

        $this->configMock->method('isChannelEnabled')->willReturn(true);
        $productMock = $this->createConfiguredMock(Product::class, ['getId' => $trackingProductNumber, 'getSku' => $sku]);
        $quoteMock   = $this->createConfiguredMock(Quote::class, [
            'getAllVisibleItems' => [$this->createConfiguredMock(Item::class, ['getPrice' => $price, 'getQty' => $qty, 'getProduct' => $productMock])]
        ]);

        $this->fieldRolesMock->expects($this->exactly(2))->method('fieldRoleToAttribute')->willReturnMap(
            [
                [$productMock, 'trackingProductNumber', $trackingProductNumber],
                [$productMock, 'masterArticleNumber', $sku],
            ]);

        $expectedProducts = [
            new TrackingProduct($trackingProductNumber, $sku, $price, $qty)
        ];

        $this->resourceMock->expects($this->once())->method('track')
            ->with($this->anything(), $this->anything(), $expectedProducts, $this->anything());

        $this->checkoutObserver->execute(new Observer(['quote' => $quoteMock]));
    }

    public function test_tracking_is_skipped_if_the_integration_is_disabled()
    {
        $this->configMock->method('isChannelEnabled')->willReturn(false);
        $observerMock = $this->createMock(Observer::class);
        $observerMock->expects($this->never())->method('getData');
        $this->checkoutObserver->execute($observerMock);
    }

    protected function setUp(): void
    {
        $this->configMock     = $this->createMock(CommunicationConfigInterface::class);
        $this->resourceMock   = $this->createConfiguredMock(ResourceInterface::class, ['track' => []]);
        $this->builderMock    = $this->createMock(Builder::class);
        $this->fieldRolesMock = $this->createMock(FieldRolesInterface::class);
        $this->resourceMock   = $this->createConfiguredMock(ResourceInterface::class, ['track' => []]);
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

        $this->checkoutObserver = new Checkout(
            $this->fieldRolesMock,
            $this->configMock,
            $this->createConfiguredMock(CredentialsFactory::class, ['create' => $this->createMock(Credentials::class)]),
            $this->createMock(SessionData::class),
            $this->builderMock,
            $this->createMock(LoggerInterface::class)
        );
    }
}
