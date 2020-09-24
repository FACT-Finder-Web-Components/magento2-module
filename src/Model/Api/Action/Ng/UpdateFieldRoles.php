<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api\Action\Ng;

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

    /** @var ClientInterfaceFactory */
    private $clientFactory;

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    /** @var Credentials */
    private $credentials;

    /** @var string */
    private $apiQuery = 'FACT-Finder version';

    public function __construct(
        FieldRolesInterface $fieldRoles,
        ClientInterfaceFactory $clientFactory,
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
        $endpoint =  ($params['serverUrl'] ?? $this->communicationConfig->getAddress())
            . sprintf('/rest/v3/search/%s', $this->communicationConfig->getChannel());

        $response = $this->clientFactory->create()->setHeaders(['Authorization' => $this->credentials->toBasicAuth()])
            ->get($endpoint, $params + ['query' => $this->apiQuery]);

        if ($response['fieldRoles'] ?? []) {
            $this->fieldRoles->saveFieldRoles($response['fieldRoles'], $scopeId);
            return true;
        }

        throw new ResponseException('FACT-Finder Response does not contain field roles information');
    }
}
