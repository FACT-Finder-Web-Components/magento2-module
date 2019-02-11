<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Framework\HTTP\ClientFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Omikron\Factfinder\Api\ClientInterface as ApiClientInterface;
use Omikron\Factfinder\Api\Config\AuthConfigInterface;
use Omikron\Factfinder\Exception\ResponseException;
use Omikron\Factfinder\Model\Api\Credentials;
use Omikron\Factfinder\Model\Api\CredentialsFactory;

class Client implements ApiClientInterface
{
    /** @var ClientFactory */
    private $httpClientFactory;

    /** @var SerializerInterface */
    private $serializer;

    /** @var AuthConfigInterface */
    private $authConfig;

    /** @var CredentialsFactory */
    private $credentialsFactory;

    public function __construct(
        ClientFactory $clientFactory,
        SerializerInterface $serializer,
        AuthConfigInterface $authConfig,
        CredentialsFactory $credentialsFactory
    ) {
        $this->httpClientFactory  = $clientFactory;
        $this->serializer         = $serializer;
        $this->authConfig         = $authConfig;
        $this->credentialsFactory = $credentialsFactory;
    }

    public function sendRequest(string $endpoint, array $params): array
    {
        $httpClient = $this->httpClientFactory->create();

        try {
            $params = ['format' => 'json'] + $params + $this->getCredentials($this->authConfig)->toArray();
            $query  = preg_replace('#products%5B\d+%5D%5B(.+?)%5D=#', '\1=', http_build_query($params));

            $httpClient->get($endpoint . '?' . $query);
            if ($httpClient->getStatus() >= 200 && $httpClient->getStatus() < 300) {
                return $this->serializer->unserialize($httpClient->getBody());
            }

            throw new ResponseException($httpClient->getBody(), $httpClient->getStatus());
        } catch (\InvalidArgumentException $e) {
            throw new ResponseException($httpClient->getBody(), $httpClient->getStatus(), $e);
        }
    }

    private function getCredentials(AuthConfigInterface $config): Credentials
    {
        return $this->credentialsFactory->create([
            'username' => $config->getUsername(),
            'password' => $config->getPassword(),
            'prefix'   => $config->getAuthenticationPrefix(),
            'postfix'  => $config->getAuthenticationPostfix(),
        ]);
    }
}
