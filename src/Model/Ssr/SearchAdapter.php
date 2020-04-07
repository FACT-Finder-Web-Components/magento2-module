<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Ssr;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\ViewModel\Communication;

class SearchAdapter
{
    /** @var ClientInterface */
    private $client;

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    /** @var Communication */
    private $params;

    /** @var PriceCurrencyInterface */
    private $priceCurrency;

    /** @var FieldRolesInterface */
    private $fieldRoles;

    public function __construct(
        ClientInterface $client,
        CommunicationConfigInterface $communicationConfig,
        PriceCurrencyInterface $priceCurrency,
        FieldRolesInterface $fieldRoles,
        Communication $params
    ) {
        $this->client              = $client;
        $this->communicationConfig = $communicationConfig;
        $this->priceCurrency       = $priceCurrency;
        $this->fieldRoles          = $fieldRoles;
        $this->params              = $params;
    }

    public function search(string $channel, string $query = '*', array $params = []): array
    {
        $endpoint = $this->communicationConfig->getAddress() . '/Search.ff';
        $params   = ['channel' => $channel, 'query' => $query] + $params;

        $priceField   = $this->fieldRoles->getFieldRole('price');
        $searchResult = $this->client->sendRequest($endpoint, $params)['searchResult'];

        $searchResult['records'] = array_map(function (array $record) use ($priceField): array {
            $record['record'] = $this->getFormattedPrice($record['record'], $priceField) + $record['record'];
            return $record;
        }, $searchResult['records'] ?? []);

        return $searchResult;
    }

    protected function getFormattedPrice(array $record, string $priceField): array
    {
        return [
            '__ORIG_PRICE__' => $record[$priceField],
            $priceField      => $this->priceCurrency->format($record[$priceField], false),
        ];
    }
}
