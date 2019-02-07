<?php

declare(strict_types = 1);

namespace Omikron\Factfinder\Model;

use Magento\Framework\HTTP\ClientFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Omikron\Factfinder\Api\ClientInterface as FactFinderClientInterface;
use Omikron\Factfinder\Api\Config\AuthConfigInterface;
use Omikron\Factfinder\Exception\RequestException;
use Omikron\Factfinder\Api\RequestExceptionInterface;

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
     * @throws RequestExceptionInterface
     */
    public function sendRequest(string $endpoint, array $params) : array
    {
        try {
            $params     = ['format' => 'json'] + $this->getAuthArray($params) + $params;
            $curlClient = $this->httpClientFactory->create();
            $curlClient->get($endpoint . '?' . http_build_query($params));

            if ($curlClient->getStatus() != 200) {
                throw new RequestException($curlClient->getBody(), $curlClient->getStatus());
            }

            return $this->serializer->unserialize($curlClient->getBody());
        } catch (\Exception $e) {
            throw new RequestException($curlClient->getBody(), $curlClient->getStatus(), $e);
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
