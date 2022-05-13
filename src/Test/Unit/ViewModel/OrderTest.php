<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\ViewModel;

use Magento\Checkout\Model\Session;
use Magento\Framework\DataObject;
use Magento\Sales\Model\Order as OrderModel;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\ViewModel\Order;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers Order
 */
class OrderTest extends TestCase
{
    private Order $order;

    /** @var MockObject|Item[]  */
    private array $orderItemsMock;

    public function test_returns_order_items()
    {
        $this->assertSame($this->orderItemsMock, $this->order->getItems());
    }

    public function test_returns_channel_name()
    {
        $this->assertEquals('test-channel', $this->order->getChannel());
    }

    protected function setUp(): void
    {
        $this->orderItemsMock = array_map(
            fn (array $data): OrderModel\Item => $this->createConfiguredMock(
                OrderModel\Item::class,
                ['getProduct' => new DataObject(['sku' => $data[0], 'qty' => $data[1]])]
            ),
            [['foo', 1], ['bar', 3], ['baz', 2]]
        );

        $orderMock   = $this->createConfiguredMock(OrderModel::class, ['getAllVisibleItems' => $this->orderItemsMock]);
        $sessionMock = $this->createConfiguredMock(Session::class, ['getLastRealOrder' => $orderMock]);

        $this->order = new Order($sessionMock, $this->createConfiguredMock(CommunicationConfig::class, ['getChannel' => 'test-channel']));
    }
}
