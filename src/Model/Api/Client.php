<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\HTTP\ClientFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\ClientInterface as ApiClientInterface;
use Omikron\Factfinder\Exception\ResponseException;

class Client implements ApiClientInterface
{
    /** @var ClientFactory */
    private $httpClientFactory;

    /** @var SerializerInterface */
    private $serializer;

    /** @var array  */
    private $headers = ['Accept' => 'application/json'];

    public function __construct(
        ClientFactory $clientFactory,
        SerializerInterface $serializer
    ) {
        $this->httpClientFactory = $clientFactory;
        $this->serializer        = $serializer;
    }

    public function setHeaders(array $headers): ClientInterface
    {
        $this->headers = $this->headers + $headers;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function post(string $endpoint, array $params): array
    {
        $curl = $this->initCurl();
        $curl->addHeader('Content-Type', 'application/json');
        $curl->post($endpoint, $this->serializer->serialize($params));
        return $this->processResponse($curl);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $endpoint, array $params): array
    {
        $curl = $this->initCurl();
        $query = preg_replace('#products%5B\d+%5D%5B(.+?)%5D=#', '\1=', http_build_query($params));
        $curl->get($endpoint . '?' . $query);
        return $this->processResponse($curl);
    }

    private function initCurl(): Curl
    {
        $curl = $this->httpClientFactory->create();
        $curl->setHeaders($this->headers);

        return $curl;
    }

    /**
     * @param Curl $curl
     *
     * @return array
     * @throws ResponseException
     */
    private function processResponse(Curl $curl): array
    {
        if ($curl->getStatus() == 200) {
            if ($curl->getBody()) {
                return (array) $this->serializer->unserialize($curl->getBody());
            }
            return $curl->getHeaders();
        }

        throw new ResponseException($curl->getBody(), $curl->getStatus());
    }
}
