<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Consumer;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Exception\ResponseException;

class UpdateFieldRoles
{
    /** @var FieldRolesInterface  */
    private $fieldRoles;

    /** @var ClientInterface  */
    private $factFinderClient;

    /** @var CommunicationConfigInterface  */
    private $communicationConfig;

    /** @var SerializerInterface\ */
    private $serializer;

    /** @var string  */
    private $apiQuery = 'FACT-Finder version';

    /** @var string  */
    private $apiName = 'Search.ff';

    public function __construct(
        FieldRolesInterface $fieldRoles,
        ClientInterface $factFinderClient,
        CommunicationConfigInterface $communicationConfig,
        SerializerInterface $serializer
    ) {
        $this->fieldRoles          = $fieldRoles;
        $this->factFinderClient    = $factFinderClient;
        $this->communicationConfig = $communicationConfig;
        $this->serializer          = $serializer;
    }

    /**
     * @param int   $scopeId
     * @param array $params
     * @return bool
     * @throws ResponseException
     */
    public function execute(int $scopeId, array $params = []): bool
    {
        $params = [
                'query'   => $this->apiQuery,
                'channel' => $params['channel'] ?? $this->communicationConfig->getChannel($scopeId),
                'verbose' => true
            ] + $params;

        $endpoint = ($params['serverUrl'] ?? $this->communicationConfig->getAddress()) . '/' . $this->apiName;
        $response = $this->factFinderClient->sendRequest($endpoint, $params);

        if ($response['searchResult']['fieldRoles'] ?? []) {
            $fieldRoles =  $this->serializer->serialize($response['searchResult']['fieldRoles']);
            $this->fieldRoles->saveFieldRoles($fieldRoles, $scopeId);

            return true;
        }

        throw new ResponseException('FACT-Finder Response does not contain field roles information');
    }
}
