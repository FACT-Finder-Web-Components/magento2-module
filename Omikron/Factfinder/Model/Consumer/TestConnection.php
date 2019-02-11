<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Consumer;

use Magento\Framework\Serialize\SerializerInterface;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Exception\ApiCallException;
use Omikron\Factfinder\Exception\ResponseException;

class TestConnection
{
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
        ClientInterface $factFinderClient,
        CommunicationConfigInterface $communicationConfig,
        SerializerInterface $serializer
    ) {
        $this->factFinderClient    = $factFinderClient;
        $this->communicationConfig = $communicationConfig;
        $this->serializer          = $serializer;
    }

    /**
     * @param int   $scopeId
     * @param array $params
     * @return bool
     * @throws ApiCallException
     */
    public function execute(int $scopeId, array $params = []) : bool
    {
        $params = [
                'query'   => $this->apiQuery,
                'channel' => $params['channel'] ?? $this->communicationConfig->getChannel($scopeId),
                'verbose' => true
            ] + $params;

        $endpoint = ($params['serverUrl'] ?? $this->communicationConfig->getAddress()) . '/' . $this->apiName;

        try {
            $this->factFinderClient->sendRequest($endpoint, $params);

            return true;
        } catch (ResponseException $e) {
            throw new ApiCallException('Test connection failed', $e);
        }
    }
}
