<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Proxy;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\NotFoundException;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Exception\ResponseException;

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
        // Extract API name from path
        $endpoint = $this->getEndpoint($this->_url->getCurrentUrl());
        if (!$endpoint) {
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

    private function getEndpoint(string $currentUrl): string
    {
        preg_match('#/([A-Z][a-z]+\.ff)#', $currentUrl, $match);
        return $match[1] ?? '';
    }
}
