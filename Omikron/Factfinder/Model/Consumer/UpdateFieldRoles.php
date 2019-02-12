<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Consumer;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Exception\ResponseException;
use Omikron\Factfinder\Helper\Data;

class UpdateFieldRoles
{
    /** @var Config  */
    protected $configResource;

    /** @var ClientInterface  */
    protected $factFinderClient;

    /** @var CommunicationConfigInterface  */
    protected $communicationConfig;

    /** @var SerializerInterface\ */
    protected $serializer;

    /** @var string  */
    protected $apiQuery = 'FACT-Finder version';

    /** @var string  */
    protected $apiName = 'Search.ff';

    public function __construct(
        Config $configResource,
        ClientInterface $factFinderClient,
        CommunicationConfigInterface $communicationConfig,
        SerializerInterface $serializer
    ) {
        $this->configResource      = $configResource;
        $this->factFinderClient    = $factFinderClient;
        $this->communicationConfig = $communicationConfig;
        $this->serializer          = $serializer;
    }

    /**
     * @param int   $scopeId
     * @param array $params
     * @return array
     * @throws ResponseException
     */
    public function execute(int $scopeId, array $params = []): array
    {
        $response = [
            'success'             => false,
            'ff_error_response'   => '',
            'ff_error_stacktrace' => '',
            'ff_response_decoded' => ''
        ];

        $params = [
                'query'   => $this->apiQuery,
                'channel' => $params['channel'] ?? $this->communicationConfig->getChannel($scopeId),
                'verbose' => true
            ] + $params;

        $endpoint = ($params['serverUrl'] ?? $this->communicationConfig->getAddress()) . '/' . $this->apiName;
        $response['ff_response_decoded'] = $this->factFinderClient->sendRequest($endpoint, $params);
        $this->processResponseHasErrors($response);

        return $this->processUpdateFieldRoles($response, $scopeId);
    }

    private function processResponseHasErrors(array &$response): void
    {
        $valid = true;
        if ($response['ff_response_decoded']['error'] ?? []) {
            $response['ff_error_response'] = $response['ff_response_decoded']['error'];
            $valid = false;

            if ($response['ff_response_decoded']['stacktrace'] ?? []) {
                $response['ff_error_stacktrace'] = explode('at', $response['ff_response_decoded']['stacktrace'])[0];
            }
        }

        if (!$valid) {
            throw new ResponseException(__('FACT-Finder response contains errors. Response body is %1', $this->serializer->serialize($response)));
        }
    }

    private function processUpdateFieldRoles(array $response, int $scopeId): array
    {
        if ($response['ff_response_decoded']['searchResult']['fieldRoles'] ?? []) {
            $response['fieldRoles'] = $this->serializer->serialize($response['ff_response_decoded']['searchResult']['fieldRoles']);
            $this->configResource->saveConfig(Data::PATH_PRODUCT_FIELD_ROLE, $response['fieldRoles'], ScopeInterface::SCOPE_STORES, $scopeId);
            $response['success']    = true;

            return $response;
        } else {
            throw new ResponseException(__('FACT-Finder response does not field roles. Response body is %1', $this->serializer->serialize($response)));
        }
    }
}
