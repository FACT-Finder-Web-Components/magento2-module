<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Proxy;

use Magento\Framework\App\Action;
use Magento\Framework\Controller\Result\JsonFactory as JsonResultFactory;
use Magento\Framework\Controller\Result\RawFactory as RawResultFactory;
use Magento\Framework\Exception\NotFoundException;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\FactFinder\Communication\Resource\Builder;
use Omikron\Factfinder\Exception\ResponseException;
use Omikron\Factfinder\Model\Api\CredentialsFactory;
use Omikron\Factfinder\Model\Http\ParameterUtils;

class Call extends Action\Action
{
    /** @var JsonResultFactory */
    private $jsonResultFactory;

    /** @var RawResultFactory */
    private $rawResultFactory;

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    /** @var ParameterUtils */
    private $parameterUtils;

    /** @var CredentialsFactory */
    private $credentialsFactory;

    public function __construct(
        Action\Context $context,
        JsonResultFactory $jsonResultFactory,
        RawResultFactory $rawResultFactory,
        CommunicationConfigInterface $communicationConfig,
        ParameterUtils $parameterUtils,
        CredentialsFactory $credentialsFactory
    ) {
        parent::__construct($context);
        $this->jsonResultFactory   = $jsonResultFactory;
        $this->rawResultFactory    = $rawResultFactory;
        $this->communicationConfig = $communicationConfig;
        $this->parameterUtils      = $parameterUtils;
        $this->credentialsFactory  = $credentialsFactory;
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
            $endpoint = $this->communicationConfig->getAddress() . '/' . $endpoint;
            $params   = $this->parameterUtils->fixedGetParams($this->getRequest()->getParams());

            $response = (new Builder())
                ->withCredentials($this->credentialsFactory->create())
                ->withServerUrl($this->communicationConfig->getAddress())
                ->client()
                ->getRequest($endpoint, $params);

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
