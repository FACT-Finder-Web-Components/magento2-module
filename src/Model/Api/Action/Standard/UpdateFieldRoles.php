<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api\Action\Standard;

use Omikron\Factfinder\Api\Action\UpdateFieldRolesInterface;
use Omikron\Factfinder\Api\ClientInterfaceFactory;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Exception\ResponseException;
use Omikron\Factfinder\Model\Api\Credentials;

class UpdateFieldRoles implements UpdateFieldRolesInterface
{
    /** @var FieldRolesInterface */
    private $fieldRoles;

    /** @var ClientFactory */
    private $clientFactory;

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    /** @var Credentials */
    private $credentials;

    /** @var string */
    private $apiQuery = 'FACT-Finder version';

    /** @var string */
    private $apiName = 'Search.ff';

    public function __construct(
        ClientInterfaceFactory $clientFactory,
        FieldRolesInterface $fieldRoles,
        CommunicationConfigInterface $communicationConfig,
        Credentials $credentials
    ) {
        $this->fieldRoles          = $fieldRoles;
        $this->clientFactory       = $clientFactory;
        $this->communicationConfig = $communicationConfig;
        $this->credentials         = $credentials;
    }

    /**
     * @param int|null $scopeId
     * @param array    $params
     *
     * @return bool
     * @throws ResponseException
     */
    public function execute(int $scopeId = null, array $params = []): bool
    {
        $default = [
            'query'   => $this->apiQuery,
            'channel' => $this->communicationConfig->getChannel($scopeId),
            'verbose' => true,
        ];

        $endpoint = ($params['serverUrl'] ?? $this->communicationConfig->getAddress()) . '/' . $this->apiName;
        $response = $this->clientFactory->create()->get($endpoint, $this->credentials->toArray() + $params + $default);

        if ($response['searchResult']['fieldRoles'] ?? []) {
            $this->fieldRoles->saveFieldRoles($response['searchResult']['fieldRoles'], $scopeId);

            return true;
        }

        throw new ResponseException('FACT-Finder Response does not contain field roles information');
    }
}
