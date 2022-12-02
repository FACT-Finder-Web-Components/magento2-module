<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Ssr;

use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Client\ClientException;
use Omikron\FactFinder\Communication\Resource\AdapterFactory;
use Omikron\FactFinder\Communication\Version;
use Omikron\Factfinder\Model\Api\CredentialsFactory;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Psr\Http\Message\ResponseInterface;

class SearchAdapter
{
    public function __construct(
        private readonly ClientBuilder $clientBuilder,
        private readonly CommunicationConfig $communicationConfig,
        private readonly CredentialsFactory $credentialsFactory,
        private readonly PriceFormatter $priceFormatter
    ) {}

    public function search(string $paramString, bool $navigationRequest): array
    {
        $client = $this->clientBuilder
            ->withServerUrl($this->communicationConfig->getAddress())
            ->withCredentials($this->credentialsFactory->create())
            ->withVersion($this->communicationConfig->getVersion())
            ->build();

        $endpoint = $this->createEndpoint($paramString, $navigationRequest);
        $response = $client->request('GET', $endpoint);

        if (!$response) {
            throw new ClientException('The response was empty');
        }

        return $this->priceFormatter->format($this->searchResult($response));
    }

    private function searchResult(ResponseInterface $response): array
    {
        return json_decode((string) $response->getBody(), true);
    }

    private function createEndpoint(string $paramString, bool $navigationRequest)
    {
        $channel  = $this->communicationConfig->getChannel();
        $endpoint = $navigationRequest ? 'navigation' : 'search';

        return $this->communicationConfig->getVersion() == Version::NG
            ? "rest/v4/{$endpoint}/{$channel}?{$paramString}"
            : "Search.ff?channel={$channel}&{$paramString}&format=json";
    }
}
