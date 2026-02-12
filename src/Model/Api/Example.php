<?php
declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Model\Quote\Item;
use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\Factfinder\Model\Config\AuthConfig;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\SessionData;

class Example
{
    public function __construct(
        private readonly SessionData $sessionData,
        private readonly CommunicationConfig $communicationConfig,
        private readonly ClientBuilder $clientBuilder,
        private readonly AuthConfig $authConfig,
        private readonly CheckoutSession $checkoutSession,
    ) {
    }

    public function getPredictiveBasketSkus(): array
    {
        $cartSkus = array_map(
            static fn (Item $item): string => $item->getProduct()->getSku(),
            $this->checkoutSession->getQuote()->getItems() ?? []
        );

        $baseParams = [
            'idsOnly' => true,
            'userId'  => $this->sessionData->getUserId(),
        ];

        $queryString = http_build_query($baseParams, '', '&', PHP_QUERY_RFC3986);

        foreach ($cartSkus as $sku) {
            $queryString .= '&blacklist=' . rawurlencode($sku);
        }

        $client = $this->clientBuilder
            ->withServerUrl($this->communicationConfig->getAddress())
            ->withApiKey($this->authConfig->getApiKey())
            ->withVersion($this->communicationConfig->getVersion())
            ->build();

        $result = $client->request(
            'GET',
            'rest/v5/predictivebasket/dev_kastner?' . $queryString
        );

        return array_column($result['hits'] ?? [], 'id');
    }
}
