<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Proxy;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\NotFoundException;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Exception\ResponseException;

/**
 * Class Call
 * Forward the ff-api calls to factfinder
 */
class Call extends \Magento\Framework\App\Action\Action
{
    /** @var JsonFactory */
    private $jsonResultFactory;

    /** @var ClientInterface */
    private $apiClient;

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        ClientInterface $apiClient,
        CommunicationConfigInterface $communicationConfig
    ) {
        parent::__construct($context);
        $this->jsonResultFactory   = $jsonResultFactory;
        $this->apiClient           = $apiClient;
        $this->communicationConfig = $communicationConfig;
    }

    public function execute()
    {
        // extract api name from path
        $identifier = trim($this->getRequest()->getPathInfo(), '/');
        $endpoint   = substr($identifier, strpos($identifier, '/') + 1);

        // return 404 if api name schema does not match
        if (!preg_match('#^[A-Z][A-z]+\.ff$#', $endpoint)) {
            throw new NotFoundException(__('Endpoint missing'));
        }

        $result = $this->jsonResultFactory->create();

        try {
            $endpoint   = $this->communicationConfig->getAddress() . '/' . $endpoint;
            $ffResponse = $this->apiClient->sendRequest($endpoint, $this->getRequest()->getParams());
            $result->setData($ffResponse);
        } catch (ResponseException $e) {
            $result->setJsonData($e->getMessage());
        }

        return $result;
    }
}
