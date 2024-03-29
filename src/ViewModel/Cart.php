<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Quote\Model\Quote\Item as QuoteItem;

class Cart implements ArgumentInterface
{
    /**
     * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
     * phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine
     */
    public function __construct(private readonly Session $checkoutSession) {}

    /**
     * @return QuoteItem[]
     */
    public function getItems(): array
    {
        try {
            return $this->checkoutSession->getQuote()->getAllVisibleItems();
        } catch (LocalizedException $e) {
            return [];
        }
    }

    /**
     * @return string[]
     */
    public function getItemIds(): array
    {
        return array_unique(array_map(fn (QuoteItem $quoteItem) => (string) $quoteItem->getProduct()->getData('sku'), $this->getItems()));
    }
}
