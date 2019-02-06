<?php

declare(strict_types = 1);

namespace Omikron\Factfinder\Model\Consumer;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
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

    public function execute(array $params = [], string $scopeId = null) : array
    {
        $response = [
            'success'             => false,
            'ff_error_response'   => '',
            'ff_error_stacktrace' => '',
            'ff_response_decoded' => ''
        ];

        $params = [
                'query'   => $this->apiQuery,
                'channel' => $this->communicationConfig->getChannel($scopeId),
                'verbose' => true
            ] + $params;

        $endpoint = $this->communicationConfig->getAddress() . $this->apiName;
        $response['ff_response_decoded'] = $this->factFinderClient->sendRequest($endpoint, $params);

        if (!$this->processResponseErrors($response)) {
            return $response;
        }

        if ($response['ff_response_decoded']['searchResult']['fieldRoles'] ?? []) {
            $response['fieldRoles'] = $this->serializer->serialize($response['ff_response_decoded']['searchResult']['fieldRoles']);
            $this->configResource->saveConfig(Data::PATH_PRODUCT_FIELD_ROLE, $response['fieldRoles'], ScopeInterface::SCOPE_STORES, $scopeId);
            $response['success']    = true;
        } else {
            throw new \Exception(__('FACT-Finder response does not contain all required fields. Response %1', $this->serializer->serialize($response)));
        }

        return $response;
    }

    /**
     * @param array $response
     * @return bool
     */
    private function processResponseErrors(array &$response) : bool
    {
        $valid = true;
        if ($response['ff_response_decoded']['error'] ?? []) {
            $response['ff_error_response'] = $response['ff_response_decoded']['error'];
            $valid = false;

            if ($response['ff_response_decoded']['stacktrace'] ?? []) {
                $response['ff_error_stacktrace'] = explode('at', $response['ff_response_decoded']['stacktrace'])[0];
            }
        }

        return $valid;
    }
}
