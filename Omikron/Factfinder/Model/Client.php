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
            $params         = ['format' => 'json'] + $params + $this->getAuthArray();
            $curlClient     = $this->httpClientFactory->create();

            $curlClient->addHeader('Accept-encoding: gzip', 'deflate');
            $curlClient->get($endpoint . http_build_query($params));

            if ($curlClient->getStatus() != 200) {
                throw  new RequestException($curlClient->getBody(), $curlClient->getStatus());
            }
            // Receive server response
            return $this->serializer->unserialize($this->httpClient->getBody());
        } catch (\Exception $e) {
            throw  new RequestException($curlClient->getBody(), $curlClient->getStatus(), $e);
        }
    }

    /**
     * Returns the authentication values as array
     *
     * @return array
     */
    protected function getAuthArray() : array
    {
        $time         = round(microtime(true) * 1000);
        $password     = $this->authConfig->getPassword();
        $prefix       = $this->authConfig->getAuthenticationPrefix();
        $postfix      = $this->authConfig->getAuthenticationPostfix();
        $hashPassword = md5($prefix . (string) $time . md5($password) . $postfix);

        $authArray = [
            'username'  => $this->authConfig->getUsername(),
            'timestamp' => $time,
            'password'  => $hashPassword
        ];

        return $authArray;
    }
}
