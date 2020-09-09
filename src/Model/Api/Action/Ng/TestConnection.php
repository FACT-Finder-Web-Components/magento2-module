<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api\Ng\Action;

use Omikron\Factfinder\Api\Action\TestConnectionInterface;
use Omikron\Factfinder\Api\ClientInterfaceFactory;
use Omikron\Factfinder\Exception\ResponseException;
use Omikron\Factfinder\Model\Api\Credentials;

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
     * @param string      $serverUrl
     * @param array       $params
     *
     * @return bool
     * @throws ResponseException
     */
    public function execute(string $serverUrl, array $params): bool
    {
        $endpoint = rtrim($serverUrl, '/') . sprintf('/rest/v3/search/%s', $params['channel']);
        $this->clientFactory->create()->setHeaders(['Authorization' => $this->credentials->toBasicAuth()])
            ->get($endpoint, $params + ['query' => $this->apiQuery]);

        return true;
    }
}
