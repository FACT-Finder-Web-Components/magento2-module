<?php

declare(strict_types = 1);

namespace Omikron\Factfinder\Model;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\HTTP\ClientInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\ClientInterface as FactFinderClientInterface;
use Magento\Config\Model\ResourceModel\Config;
use Omikron\Factfinder\Helper\Communication as CommunicationHelper;
use Omikron\Factfinder\Helper\Data as ConfigHelper;
use Psr\Log\LoggerInterface;

class Client implements FactFinderClientInterface
{
    // API DATA
    const API_NAME  = 'Search.ff';
    const API_QUERY = 'FACT-Finder version';

    /** @var Config */
    protected $configResource;

    /** @var ClientInterface */
    protected $httpClient;

    /** @var SerializerInterface */
    protected $serializer;

    /** @var CommunicationHelper */
    protected $communicationHelper;

    /** @var ConfigHelper */
    protected $configHelper;

    /** @var LoggerInterface */
    protected $logger;

    public function __construct(
        Config $configResource,
        ClientInterface $httpClient,
        SerializerInterface $serializer,
        ConfigHelper $configHelper,
        CommunicationHelper $communicationHelper,
        LoggerInterface $logger
    ) {
        $this->configResource      = $configResource;
        $this->httpClient          = $httpClient;
        $this->serializer          = $serializer;
        $this->configHelper        = $configHelper;
        $this->communicationHelper = $communicationHelper;
        $this->logger              = $logger;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $apiName
     * @param string $paramsQuery
     * @return string
     * @change all method call to pass $params argument as array
     */
    public function sendToFF(string $apiName, string $paramsQuery) : string
    {
        $authentication = $this->getAuthArray();
        $address        = $this->communicationHelper->getAddress();
        $url            = $address . $apiName . '?format=json&' . http_build_query($authentication) . '&' . $paramsQuery;

        // Send HTTP GET with curl
        $this->httpClient->setOption(CURLOPT_ENCODING, 'Accept-encoding: gzip, deflate');
        $this->httpClient->setOption(CURLOPT_RETURNTRANSFER, 1);
        $this->httpClient->get($url);
        // Receive server response
        $response = $this->httpClient->getBody();

        return $response;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $storeId
     * @return bool
     */
    public function pushImport(string $storeId) : bool
    {
        $channel     = $this->communicationHelper->getChannel($storeId);
        $importTypes = $this->communicationHelper->getPushImportTypes($storeId);

        if (empty($importTypes)) {
            return false;
        }

        $responseJson = [];
        $params       = [
            'channel'  => $channel,
            'format'   => 'json',
            'quiet'    => 'true',
            'download' => 'true'
        ];

        foreach ($importTypes as $type) {
            $params['type'] = $type;
            $response = $this->sendToFF('Import.ff', http_build_query($params));
            try {
                $responseJson = array_merge_recursive($responseJson, $this->serializer->unserialize($response, true));
            } catch (\InvalidArgumentException $e) {
                $this->logError($e, $response);

                return false;
            }
        }

        $this->logResponse($this->serializer->serialize($responseJson));

        if ($responseJson['errors'] ?? $responseJson['error'] ?? []) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $storeID
     * @return array
     * @throws \Exception
     */
    public function updateFieldRoles(string $storeId) : array
    {
        $result = [
            'success' => false,
            'ff_error_response' => '',
            'ff_error_stacktrace' => '',
        ];
        $response = $this->sendToFF(
            self::API_NAME, http_build_query(
                [
                    'query'   => self::API_QUERY,
                    'channel' => $this->communicationHelper->getChannel($storeId),
                    'verbose' => 'true'
                ]
            )
        );

        try {
            $result['ff_response_decoded'] = $this->serializer->unserialize($response, true);
        } catch (\InvalidArgumentException $e) {
            $this->logError($e, $response);
            $result['success'] = false;

            return $result;
        }

        if ($result['ff_response_decoded']['error'] ?? []) {
            $result['ff_error_response'] = $result['ff_response_decoded']['error'];
        }

        if ($result['ff_response_decoded']['stacktrace'] ?? []) {
            $result['ff_error_stacktrace'] = explode('at', $result['ff_response_decoded']['stacktrace'])[0];
        }

        if ($result['ff_response_decoded']['searchResult']['fieldRoles'] ?? []) {
            $result['fieldRoles'] =
                $this->serializer->serialize($result['ff_response_decoded']['searchResult']['fieldRoles']);
            $result['success']    = true;
            $this->configResource->saveConfig(
                ConfigHelper::PATH_TRACKING_PRODUCT_NUMBER_FIELD_ROLE, $result['fieldRoles'],
                ScopeInterface::SCOPE_STORES, $storeId
            );
        } else {
            throw new \Exception(
                __(
                    'FACT-Finder response does not contain all required fields. Response %1',
                    $this->serializer->serialize($result)
                )
            );
        }

        return $result;
    }

    /**
     * Returns the authentication values as array
     *
     * @return array
     */
    protected function getAuthArray() : array
    {
        $time         = round(microtime(true) * 1000);
        $password     = $this->communicationHelper->getPassword();
        $prefix       = $this->communicationHelper->getAuthenticationPrefix();
        $postfix      = $this->communicationHelper->getAuthenticationPostfix();
        $hashPassword = md5($prefix . (string) $time . md5($password) . $postfix);

        $authArray = [
            'username'  => $this->communicationHelper->getUsername(),
            'timestamp' => $time,
            'password'  => $hashPassword
        ];

        return $authArray;
    }

    private function logResponse(string $response)
    {
        if ($this->configHelper->isLoggingEnabled()) {
            $this->logger->info(__('FACT-Finder response : %1', $response));
        }
    }

    /**
     * @param \Exception $exception
     * @param string     $response
     */
    private function logError(\Exception $exception, string $response)
    {
        if ($this->configHelper->isLoggingEnabled()) {
            $this->logger->error(
                __(
                    'Exception %1  thrown at %2. FACT-Finder response : %3',
                    $exception->getMessage(), $exception->getTraceAsString(), $response
                )
            );
        }
    }
}
