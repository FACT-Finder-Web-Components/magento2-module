<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api;

use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Exception\ResponseException;

class UpdateFieldRoles
{
    /** @var FieldRolesInterface */
    private $fieldRoles;

    /** @var ClientInterface */
    private $factFinderClient;

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    /** @var string */
    private $apiQuery = 'FACT-Finder version';

    /** @var string */
    private $apiName = 'Search.ff';

    public function __construct(
        FieldRolesInterface $fieldRoles,
        ClientInterface $factFinderClient,
        CommunicationConfigInterface $communicationConfig
    ) {
        $this->fieldRoles          = $fieldRoles;
        $this->factFinderClient    = $factFinderClient;
        $this->communicationConfig = $communicationConfig;
    }

    /**
     * @param int   $scopeId
     * @param array $params
     *
     * @return bool
     * @throws ResponseException
     */
    public function execute(int $scopeId, array $params = []): bool
    {
        $default = [
            'query'   => $this->apiQuery,
            'channel' => $this->communicationConfig->getChannel($scopeId),
            'verbose' => true,
        ];

        $endpoint = ($params['serverUrl'] ?? $this->communicationConfig->getAddress()) . '/' . $this->apiName;
        $response = $this->factFinderClient->sendRequest($endpoint, $params + $default);

        if ($response['searchResult']['fieldRoles'] ?? []) {
            $this->fieldRoles->saveFieldRoles($response['searchResult']['fieldRoles'], $scopeId);
            return true;
        }

        throw new ResponseException('FACT-Finder Response does not contain field roles information');
    }
}
