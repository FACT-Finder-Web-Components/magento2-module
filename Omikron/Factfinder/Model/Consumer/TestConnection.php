<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Consumer;

use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Exception\ResponseException;

class TestConnection
{
    /** @var ClientInterface */
    private $apiClient;

    /** @var string */
    private $apiQuery = 'FACT-Finder version';

    public function __construct(ClientInterface $apiClient)
    {
        $this->apiClient = $apiClient;
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
        $this->apiClient->sendRequest(rtrim($serverUrl, '/') . '/Search.ff', $params + ['query' => $this->apiQuery]);
        return true;
    }
}
