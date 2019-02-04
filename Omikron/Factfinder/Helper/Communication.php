<?php

namespace Omikron\Factfinder\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\SerializerInterface;
use Omikron\Factfinder\Helper\Data as ConfigHelper;
use Psr\Log\LoggerInterface;

/**
 * Class Communication
 * Helper Class for communicating with the FACT-Finder API
 */
class Communication extends AbstractHelper
{
    // API DATA
    const API_NAME = 'Search.ff';
    const API_QUERY = 'FACT-Finder version';

    /** @var Data  */
    protected $configHelper;

    /** @var SerializerInterface  */
    protected $jsonSerializer;

    public function __construct(
        Context $context,
        ConfigHelper $helper,
        SerializerInterface $jsonSerializer
    ) {
        parent::__construct($context);
        $this->configHelper   = $helper;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Sends HTTP GET request to FACT-Finder. Returns the server response.
     *
     * @param string $apiName
     * @param string|array $params
     * @return mixed
     */
    public function sendToFF($apiName, $params)
    {
        $authentication = $this->configHelper->getAuthArray();
        $address = $this->configHelper->getAddress();

        $url = $address . $apiName . '?format=json&' . http_build_query($authentication) . '&';

        if (is_array($params)) {
            $url .= http_build_query($params);
        } else {
            $url .= $params;
        }

        // Send HTTP GET with curl
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_ENCODING, 'Accept-encoding: gzip, deflate');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        // Receive server response
        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    /**
     * Checks the connection to FACT-Finder with verbose=true and returns an array with relevant information from the response
     *
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return array
     */
    public function checkConnection($store)
    {
        $result = [];
        $result['success'] = true;
        $result['ff_error_response'] = '';
        $result['ff_error_stacktrace'] = '';
        $response = $this->sendToFF(self::API_NAME, ['query' =>  self::API_QUERY, 'channel' => $this->configHelper->getChannel($store->getId()), 'verbose' => 'true']);
        try {
            $result['ff_response_decoded'] = $this->jsonSerializer->unserialize($response, true);
        } catch (\InvalidArgumentException $e) {
            $this->logError($e, $response);
            $result['success'] = false;

            return $result;
        }

        if (!is_array($result['ff_response_decoded'])) {
            $result['ff_response_decoded'] = [];
            $result['success'] = false;
        }
        if (isset($result['ff_response_decoded']['error'])) {
            $result['ff_error_response'] = $result['ff_response_decoded']['error'];
            if(isset($result['ff_response_decoded']['stacktrace'])) $result['ff_error_stacktrace'] = explode('at', $result['ff_response_decoded']['stacktrace'])[0];
            $result['success'] = false;
        }
        if($result['success'] && isset($result['ff_response_decoded']['searchResult']) && isset($result['ff_response_decoded']['searchResult']['fieldRoles'])) {
            $result['hasFieldRoles'] = true;
            $result['fieldRoles'] = $this->jsonSerializer->serialize($result['ff_response_decoded']['searchResult']['fieldRoles']);
        }
        else {
            $result['hasFieldRoles'] = false;
            $result['fieldRoles'] = false;
        }

        return $result;
    }

    /**
     * Update trackingProductNumber field role
     *
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return array
     */
    public function updateFieldRoles($store)
    {
        $conCheck = $this->checkConnection($store);
        if($conCheck['hasFieldRoles']) {
            $this->configHelper->setFieldRoles($conCheck['fieldRoles'], $store);
        }
        return $conCheck;
    }

    /**
     * Triggers an ff import on the pushed data
     *
     * @param string $channelName
     * @param int|null $storeId
     * @return bool
     */
    public function pushImport($channelName, $storeId = null)
    {
        $importTypes = $this->configHelper->getPushImportTypes($storeId);
        if (empty($importTypes)) {
            return false;
        }
        $responseJson = [];
        foreach ($importTypes as $type) {
            $response = $this->sendToFF('Import.ff', ['channel' => $channelName, 'type' => $type, 'format' => 'json' , 'quiet' => 'true', 'download' => 'true']);
            try {
                $responseJson = array_merge_recursive($responseJson, $this->jsonSerializer->unserialize($response, true));
            } catch (\InvalidArgumentException $e) {
                $this->logError($e, $response);

                return false;
            }
        }

        $this->logResponse($this->jsonSerializer->serialize($responseJson));

        if (is_array($responseJson) && isset($responseJson['errors']) &&
            (!empty($responseJson['errors']) || !empty($responseJson['error']))) {
            return false;
        }

        return true;
    }

    private function logResponse(string $response)
    {
        if ($this->configHelper->isLoggingEnabled()) {
            $this->_logger->info(__('[PUSH IMPORT]:: FACT-Finder response : %1', $response));
        }
    }

    /**
     * @param \Exception $exception
     * @param string     $response
     */
    private function logError(\Exception $exception, string $response)
    {
        if ($this->configHelper->isLoggingEnabled()) {
            $this->_logger->error(
                __(
                    '[PUSH IMPORT]::Exception %1  thrown at %2. FACT-Finder response : %3',
                    $exception->getMessage(), $exception->getTraceAsString(), $response
                )
            );
        }
    }
}
