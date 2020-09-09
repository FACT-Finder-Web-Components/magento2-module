<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api\Action\Standard;

use Omikron\Factfinder\Api\Action\TestConnectionInterface;
use Omikron\Factfinder\Exception\ResponseException;
use Omikron\Factfinder\Model\Api\Credentials;
use Omikron\Factfinder\Api\ClientInterfaceFactory;

class TestConnection implements TestConnectionInterface
{
    /** @var ClientInterfaceFactory */
    private $clientFactory;

    /** @var Credentials  */
    private $credentials;

    /** @var string */
    private $apiQuery = 'FACT-Finder version';

    public function __construct(ClientInterfaceFactory $clientFactory, Credentials $credentials)
    {
        $this->clientFactory = $clientFactory;
        $this->credentials   = $credentials;
    }

    /**
     * @param string $serverUrl
     * @param array  $params
     *
     * @return bool
     * @throws ResponseException
     */
    public function execute(string $serverUrl, array $params): bool
    {
        $params   = $this->credentials->toArray() + $params + ['format' => 'json', 'query' => $this->apiQuery];
        $endpoint =  rtrim($serverUrl, '/') . '/Search.ff';
        $this->clientFactory->create()->get($endpoint, $params);

        return true;
    }
}
