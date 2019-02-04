<?php

namespace Omikron\Factfinder\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Omikron\Factfinder\Helper\Data as ConfigHelper;
use Psr\Log\LoggerInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Communication
 * Helper Class for communicating with the FACT-Finder API
 */
class Communication extends AbstractHelper
{
    // API DATA
    const API_NAME = 'Search.ff';
    const API_QUERY = 'FACT-Finder version';

    /**
     * @var Data
     */
    protected $configHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var SerializerInterface
     */
    protected $jsonSerializer;

    /**
     * Communication constructor.
     *
     * @param Context             $context
     * @param Data                $helper
     * @param SerializerInterface $jsonSerializer
     * @param LoggerInterface     $logger
     */
    public function __construct(
        Context $context,
        ConfigHelper $helper,
        SerializerInterface $jsonSerializer,
        LoggerInterface $logger
    )
    {
        parent::__construct($context);
        $this->configHelper   = $helper;
        $this->logger         = $logger;
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
        $result['ff_response_decoded'] = $this->jsonSerializer->unserialize($this->sendToFF(self::API_NAME, ['query' =>  self::API_QUERY, 'channel' => $this->configHelper->getChannel($store->getId()), 'verbose' => 'true']), true);

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
        $response_json = [];
        foreach ($importTypes as $type) {
            $response_json = array_merge_recursive($response_json, $this->jsonSerializer->unserialize($this->sendToFF('Import.ff', ['channel' => $channelName, 'type' => $type, 'format' => 'json' , 'quiet' => 'true', 'download' => 'true']), true));
        }
        $this->_logger->info(
            __('[PUSH IMPORT]:: Push for store %1. Response from FACT-Finder server  : %2', $storeId, $this->jsonSerializer->serialize($response_json))
        );

        if (is_array($response_json) && isset($response_json['errors']) &&
            (!empty($response_json['errors']) || !empty($response_json['error']))) {
            return false;
        }

        return true;
    }
}
