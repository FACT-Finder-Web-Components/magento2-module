<?php

namespace Omikron\Factfinder\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Communication
 * Helper Class for communicating with the Factfinder API
 *
 * @package Omikron\Factfinder\Helper
 */
class Communication extends AbstractHelper
{
    // API DATA
    const API_NAME = 'Search.ff';
    const API_QUERY = 'FACT-Finder version';

    /** @var \Omikron\Factfinder\Helper\Data */
    protected $_helper;

    /** @var \Magento\Config\Model\ResourceModel\Config */
    protected $_resourceConfig;

    /**
     * Communication constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
     * @param \Omikron\Factfinder\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Omikron\Factfinder\Helper\Data $helper
    )
    {
        $this->_helper = $helper;
        $this->_resourceConfig = $resourceConfig;
        parent::__construct($context);
    }

    /**
     * Sends HTTP GET request to FACT-Finder. Returns the server response.
     *
     * @param $apiName string
     * @param $params string|array
     * @return mixed
     */
    public function sendToFF($apiName, $params)
    {
        $authentication = $this->_helper->getAuthArray();
        $address = $this->_helper->getAddress();

        $url = $address . $apiName . "?format=json&" . http_build_query($authentication) . "&";

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
        $result['ff_error_response'] = "";
        $result['ff_error_stacktrace'] = "";
        $result['ff_response_decoded'] = json_decode($this->sendToFF(self::API_NAME, ['query' =>  self::API_QUERY, 'channel' => $this->_helper->getChannel($store->getId()), 'verbose' => 'true']), true);
        
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
            $result['fieldRoles'] = json_encode($result['ff_response_decoded']['searchResult']['fieldRoles']);
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
            $this->_helper->setFieldRoles($conCheck['fieldRoles'], $store);
        }
        return $conCheck;
    }

    /**
     * Triggers an ff import on the pushed data
     *
     * @param string $channelName
     * @return bool
     */
    public function pushImport($channelName)
    {
        $response_json = json_decode($this->sendToFF('Import.ff', ['channel' => $channelName, 'type' => 'suggest', 'format' => 'json' , 'quiet' => 'true', 'download' => 'true']), true);

        if (is_array($response_json)) {
            if (isset($response_json['errors']) && !empty($response_json['errors'])) {
                return false;
            }
            elseif (isset($response_json['error']) && !empty($response_json['error'])) {
                return false;
            }
        }

        return true;
    }
}
