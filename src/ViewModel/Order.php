<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Model\Order\Item as OrderItem;
use Omikron\Factfinder\Model\Config\CommunicationConfig;

class Order implements ArgumentInterface
{
    private Session $checkoutSession;
    private CommunicationConfig $communicationConfig;

    public function __construct(Session $checkoutSession, CommunicationConfig $communicationConfig)
    {
        $this->checkoutSession     = $checkoutSession;
        $this->communicationConfig = $communicationConfig;
    }

    /**
     * @return OrderItem[]
     */
    public function getItems(): array
    {
        try {
            return $this->checkoutSession->getLastRealOrder()->getAllVisibleItems();
        } catch (LocalizedException $e) {
            return [];
        }
    }

    public function getChannel(): string
    {
        return $this->communicationConfig->getChannel();
    }
}
