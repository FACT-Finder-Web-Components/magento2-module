<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Proxy;

use GuzzleHttp\ClientFactory;
use Magento\Framework\App\Action;
use Magento\Framework\Controller\Result\JsonFactory as JsonResultFactory;
use Magento\Framework\Controller\Result\RawFactory as RawResultFactory;
use Magento\Framework\Exception\NotFoundException;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Client\ClientException;
use Omikron\Factfinder\Model\Api\CredentialsFactory;

class Call extends Action\Action
{
    /** @var JsonResultFactory */
    private $jsonResultFactory;

    /** @var RawResultFactory */
    private $rawResultFactory;

    /** @var ClientFactory */
    private $clientFactory;

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    /** @var CredentialsFactory */
    private $credentialsFactory;

    /** @var ClientBuilder */
    private $clientBuilder;

    public function __construct(
        Action\Context $context,
        JsonResultFactory $jsonResultFactory,
        RawResultFactory $rawResultFactory,
        ClientFactory $clientFactory,
        CommunicationConfigInterface $communicationConfig,
        CredentialsFactory $credentialsFactory,
        ClientBuilder $clientBuilder
    ) {
        parent::__construct($context);
        $this->jsonResultFactory   = $jsonResultFactory;
        $this->rawResultFactory    = $rawResultFactory;
        $this->clientFactory       = $clientFactory;
        $this->communicationConfig = $communicationConfig;
        $this->credentialsFactory  = $credentialsFactory;
        $this->clientBuilder       = $clientBuilder;
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
            $client   = $this->clientBuilder
                ->withCredentials($this->credentialsFactory->create())
                ->withServerUrl($this->communicationConfig->getAddress())
                ->withVersion($this->communicationConfig->getVersion())
                ->build();

            if ($this->getRequest()->getMethod() === 'POST') {
                $response = $client->request(
                    'POST',
                    $endpoint,
                    ['body' => $this->getRequest()->getContent(), 'headers' => ['Content-Type' => 'application/json']]
                );
            } else if ($this->getRequest()->getMethod() === 'GET') {
                $response = $client->request(
                    'GET',
                    $endpoint . '?' . (string) parse_url($this->_url->getCurrentUrl(), PHP_URL_QUERY)
                );
            } else {
                throw new \HttpRequestMethodException(__(sprintf('Method %s is not supported', $this->getRequest()->getMethod())));
            }

            $result->setJsonData($response->getBody()->getContents());
        } catch (ClientException $e) {
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
