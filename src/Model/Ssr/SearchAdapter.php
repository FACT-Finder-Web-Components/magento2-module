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
    /** @var ClientBuilder */
    private $clientBuilder;

    /** @var CommunicationConfig */
    private $communicationConfig;

    /** @var CredentialsFactory */
    private $credentialsFactory;

    /** @var PriceFormatter */
    private $priceFormatter;

    public function __construct(
        ClientBuilder $clientBuilder,
        CommunicationConfig $communicationConfig,
        CredentialsFactory $credentialsFactory,
        PriceFormatter $priceFormatter
    ) {
        $this->clientBuilder       = $clientBuilder;
        $this->communicationConfig = $communicationConfig;
        $this->credentialsFactory  = $credentialsFactory;
        $this->priceFormatter      = $priceFormatter;
    }

    public function search(string $paramString): array
    {
        $client = $this->clientBuilder
            ->withServerUrl($this->communicationConfig->getAddress())
            ->withCredentials($this->credentialsFactory->create())
            ->withVersion($this->communicationConfig->getVersion())
            ->build();

        $endpoint = $this->createEndpoint($paramString);
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

    private function createEndpoint(string $paramString)
    {
        $channel  = $this->communicationConfig->getChannel();
        $endpoint = (bool) preg_match('/navigation=true/', $paramString) ? 'navigation' : 'search';

        return $this->communicationConfig->getVersion() == Version::NG
            ? "rest/v4/{$endpoint}/{$channel}?{$paramString}"
            : "Search.ff?channel={$channel}&{$paramString}&format=json";
    }
}
