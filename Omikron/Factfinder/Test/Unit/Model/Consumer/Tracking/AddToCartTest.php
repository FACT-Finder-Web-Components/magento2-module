<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Model\Consumer;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Helper\Data;
use Omikron\Factfinder\Helper\Product;
use Omikron\Factfinder\Model\Consumer\Tracking\AddToCart;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AddToCartTest extends TestCase
{
    /** @var MockObject|ClientInterface */
    private $factFinderClientMock;

    /** @var StoreManagerInterface|StoreManagerInterface */
    private $storeManagerMock;

    /** @var MockObject|Session */
    private $sessionMock;

    /** @var MockObject|CommunicationConfigInterface */
    private $communicationConfigMock;

    /** @var MockObject|ProductRepositoryInterface */
    private $productRepositoryMock;

    /** @var MockObject|Product*/
    private $productHelperMock;

    /** @var MockObject|Data */
    private $helperMock;

    /** @var AddToCart */
    private $addToCartTracking;

    public function test_execute_customer_id_should_be_added_from_session()
    {
        $this->communicationConfigMock->method('getChannel')->willReturn('test-channel');
        $this->communicationConfigMock->method('getAddress')->willReturn('http://fake-factfinder.com/FACT-Finder-7.3');
        $this->sessionMock->method('getCustomerId')->willReturn(123);
        $this->factFinderClientMock->expects($this->once())->method('sendRequest')->with(
            'http://fake-factfinder.com/FACT-Finder-7.3/Tracking.ff', $this->arrayHasKey('userId')
        );
        $this->addToCartTracking->execute($this->createMock(ProductInterface::class), 1);
    }

    protected function setUp()
    {
        $this->sessionMock             = $this->createMock(Session::class);
        $this->storeManagerMock        = $this->createMock(StoreManagerInterface::class);
        $this->factFinderClientMock    = $this->createMock(ClientInterface::class);
        $this->communicationConfigMock = $this->createMock(CommunicationConfigInterface::class);
        $this->productRepositoryMock   = $this->createMock(ProductRepositoryInterface::class);
        $this->productHelperMock       = $this->createMock(Product::class);
        $this->helperMock              = $this->createMock(Data::class);
        $this->storeManagerMock->method('getStore')->willReturn($this->createMock(StoreInterface::class));

        $this->addToCartTracking = new AddToCart(
            $this->sessionMock,
            $this->storeManagerMock,
            $this->factFinderClientMock,
            $this->productRepositoryMock,
            $this->communicationConfigMock,
            $this->productHelperMock,
            $this->helperMock
        );
    }
}
