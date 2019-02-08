<?php

declare(strict_types = 1);

namespace Omikron\Factfinder\Model;

use Magento\Framework\HTTP\ClientFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Omikron\Factfinder\Api\ClientInterface as FactFinderClientInterface;
use Omikron\Factfinder\Api\Config\AuthConfigInterface;
use Omikron\Factfinder\Exception\ResponseException;

class Client implements FactFinderClientInterface
{
    /** @var ClientFactory */
    protected $httpClientFactory;

    /** @var SerializerInterface */
    protected $serializer;

    /** @var AuthConfigInterface  */
    protected $authConfig;

    public function __construct(
        ClientFactory $clientFactory,
        SerializerInterface $serializer,
        AuthConfigInterface $authConfig
    ) {
        $this->httpClientFactory   = $clientFactory;
        $this->serializer          = $serializer;
        $this->authConfig = $authConfig;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $apiName
     * @param array $params
     * @return array
     * @throws ResponseException
     */
    public function sendRequest(string $endpoint, array $params) : array
    {
        $params     = ['format' => 'json'] + $this->getAuthArray($params) + $params;
        $curlClient = $this->httpClientFactory->create();
        $curlClient->get($endpoint . '?' . preg_replace('#products%5B\d+%5D%5B(.+?)%5D=#', '\1=', http_build_query($params)));

        if ($curlClient->getStatus() != 200) {
            throw new ResponseException($curlClient->getBody(), $curlClient->getStatus());
        }

        try {
            return $this->serializer->unserialize($curlClient->getBody());
        } catch (\InvalidArgumentException $e) {
            throw new ResponseException($curlClient->getBody(), $curlClient->getStatus(), $e);
        }
    }

    /**
     * Returns the authentication values as array
     * @param array $params
     * @return array
     */
    protected function getAuthArray(array $params = []) : array
    {
        $time         = round(microtime(true) * 1000);
        $password     = $params['password'] ?? $this->authConfig->getPassword();
        $prefix       = $params['authenticationPrefix'] ?? $this->authConfig->getAuthenticationPrefix();
        $postfix      = $params['authenticationPostfix'] ?? $this->authConfig->getAuthenticationPostfix();
        $hashPassword = md5($prefix . (string) $time . md5($password) . $postfix);

        $authArray = [
            'username'  => $params['username'] ?? $this->authConfig->getUsername(),
            'timestamp' => $time,
            'password'  => $hashPassword
        ];

        return $authArray;
    }
}
