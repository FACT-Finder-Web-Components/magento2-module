<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Proxy;

use Magento\Framework\App\Action;
use Magento\Framework\Controller\Result\JsonFactory as JsonResultFactory;
use Magento\Framework\Controller\Result\RawFactory as RawResultFactory;
use Magento\Framework\Exception\NotFoundException;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Exception\ResponseException;

class Call extends Action\Action
{
    /** @var JsonResultFactory */
    private $jsonResultFactory;

    /** @var RawResultFactory */
    private $rawResultFactory;

    /** @var ClientInterface */
    private $apiClient;

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    public function __construct(
        Action\Context $context,
        JsonResultFactory $jsonResultFactory,
        RawResultFactory $rawResultFactory,
        ClientInterface $apiClient,
        CommunicationConfigInterface $communicationConfig
    ) {
        parent::__construct($context);
        $this->jsonResultFactory   = $jsonResultFactory;
        $this->rawResultFactory    = $rawResultFactory;
        $this->apiClient           = $apiClient;
        $this->communicationConfig = $communicationConfig;
    }

    public function execute()
    {
        // Extract API name from path
        $endpoint = $this->getEndpoint($this->_url->getCurrentUrl());
        die($endpoint);
        if (!$endpoint) {
            throw new NotFoundException(__('Endpoint missing'));
        }

        $result = $this->jsonResultFactory->create();
        try {
            $endpoint = $this->communicationConfig->getAddress() . '/' . $endpoint;
            $response = $this->apiClient->sendRequest($endpoint, $this->getRequest()->getParams());
            $this->_eventManager->dispatch('ff_proxy_post_dispatch', [
                'endpoint' => $endpoint,
                'params'   => $this->getRequest()->getParams(),
                'response' => &$response,
            ]);
            $result->setData($response);
        } catch (ResponseException $e) {
            return $this->rawResultFactory->create()->setContents($e->getMessage());
        }

        return $result;
    }

    private function getEndpoint(string $currentUrl): string
    {
        preg_match('#/([A-Za-z]+\.ff|rest/v[^\?]*)#', $currentUrl, $match);
        return $match[1] ?? '';
    }
}
