<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Ssr;

use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Client\ClientException;
use Omikron\Factfinder\Logger\FactFinderLogger;
use Omikron\Factfinder\Model\Config\AuthConfig;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Psr\Http\Message\ResponseInterface;

class SearchAdapter
{
    public function __construct(
        private readonly ClientBuilder $clientBuilder,
        private readonly CommunicationConfig $communicationConfig,
        private readonly AuthConfig $authConfig,
        private readonly PriceFormatter $priceFormatter,
        private readonly FactFinderLogger $logger,
    ) {
    }

    public function search(string $paramString, bool $navigationRequest): array
    {
        try {
            $client = $this->clientBuilder
                ->withServerUrl($this->communicationConfig->getAddress())
                ->withApiKey($this->authConfig->getApiKey())
                ->withVersion($this->communicationConfig->getVersion())
                ->build();

            $endpoint = $this->createEndpoint($paramString, $navigationRequest);
            $response = $client->request('GET', $endpoint);
        } catch (ClientException $e) {
            $this->logger->error($e->getMessage());
        }

        if (empty($response)) {
            throw new ClientException('The response was empty or not exist. HTTP 4xx error during the request');
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
        $apiVersion  = $this->communicationConfig->getApiVersion();
        $endpoint = $navigationRequest ? 'navigation' : 'search';

        return "rest/{$apiVersion}/{$endpoint}/{$channel}?{$paramString}";
    }
}
