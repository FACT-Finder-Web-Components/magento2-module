<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Ssr;

use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Resource\AdapterFactory;
use Omikron\Factfinder\Model\Api\CredentialsFactory;

class SearchAdapter
{
    /** @var ClientBuilder */
    private $clientBuilder;

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    /** @var CredentialsFactory */
    private $credentialsFactory;

    /** @var PriceFormatter */
    private $priceFormatter;

    public function __construct(
        ClientBuilder $clientBuilder,
        CommunicationConfigInterface $communicationConfig,
        CredentialsFactory $credentialsFactory,
        PriceFormatter $priceFormatter
    ) {
        $this->clientBuilder       = $clientBuilder;
        $this->communicationConfig = $communicationConfig;
        $this->credentialsFactory  = $credentialsFactory;
        $this->priceFormatter      = $priceFormatter;
    }

    public function search(string $channel, string $query = '*', array $params = []): array
    {
        $client = $this->clientBuilder
            ->withServerUrl($this->communicationConfig->getAddress())
            ->withCredentials($this->credentialsFactory->create());

        $searchAdapter = (new AdapterFactory($client, $this->communicationConfig->getVersion()))->getSearchAdapter();
        $searchResult  = $searchAdapter->search($this->communicationConfig->getChannel(), $query, $params);
        $searchResult['records'] = $this->priceFormatter->format($searchResult);

        return $searchResult;
    }
}
