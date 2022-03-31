<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Checkout\Model\Session;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote;
use PHPUnit\Framework\TestCase;

/**
 * @covers Cart
 */
class CartTest extends TestCase
{
    private Cart $cart;

    public function test_get_item_ids()
    {
        $this->assertSame(['foo', 'bar', 'baz'], $this->cart->getItemIds());
    }

    protected function setUp(): void
    {
        $quoteItems = array_map(
            fn (string $sku): Quote\Item =>$this->createConfiguredMock(Quote\Item::class, ['getProduct' => new DataObject(['sku' => $sku])]),
            ['foo', 'bar', 'baz']
        );

        $quoteMock   = $this->createConfiguredMock(Quote::class, ['getAllVisibleItems' => $quoteItems]);
        $sessionMock = $this->createConfiguredMock(Session::class, ['getQuote' => $quoteMock]);

        $this->cart = new Cart($sessionMock);
    }
}
