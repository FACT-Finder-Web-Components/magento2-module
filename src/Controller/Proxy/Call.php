<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Proxy;

use Magento\Framework\App\Action;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory as JsonResultFactory;
use Magento\Framework\Controller\Result\RawFactory as RawResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\Factfinder\Controller\SkipCsrfValidation;
use Omikron\Factfinder\Model\Api\CredentialsFactory;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class Call extends Action\Action implements Action\HttpGetActionInterface, Action\HttpPostActionInterface, CsrfAwareActionInterface
{
    use SkipCsrfValidation;

    /** @var JsonResultFactory */
    private $jsonResultFactory;

    /** @var RawResultFactory */
    private $rawResultFactory;

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
        CommunicationConfigInterface $communicationConfig,
        CredentialsFactory $credentialsFactory,
        ClientBuilder $clientBuilder
    ) {
        parent::__construct($context);
        $this->jsonResultFactory   = $jsonResultFactory;
        $this->rawResultFactory    = $rawResultFactory;
        $this->communicationConfig = $communicationConfig;
        $this->credentialsFactory  = $credentialsFactory;
        $this->clientBuilder       = $clientBuilder;
    }

    public function execute()
    {
        $url = $this->_url->getCurrentUrl();

        // Extract API name from path
        $endpoint = $this->getEndpoint($url);
        if (!$endpoint) {
            throw new NotFoundException(__('Endpoint missing'));
        }

        try {
            $client = $this->clientBuilder
                ->withCredentials($this->credentialsFactory->create())
                ->withServerUrl($this->communicationConfig->getAddress())
                ->withVersion($this->communicationConfig->getVersion())
                ->build();

            $method = $this->getRequest()->getMethod();
            switch ($method) {
                case 'GET':
                    $query = (string) parse_url($url, PHP_URL_QUERY); // phpcs:ignore
                    return $this->returnJson($client->request('GET', $endpoint . '?' . $query));
                case 'POST':
                    return $this->returnJson($client->request('POST', $endpoint, [
                        'body'    => $this->getRequest()->getContent(),
                        'headers' => ['Content-Type' => 'application/json'],
                    ]));
                default:
                    throw new LocalizedException(__(sprintf('HTTP Method %s is not supported', $method)));
            }
        } catch (ClientExceptionInterface $e) {
            return $this->rawResultFactory->create()->setContents($e->getMessage());
        }
    }

    private function getEndpoint(string $currentUrl): string
    {
        preg_match('#/([A-Za-z]+\.ff|rest/v[^\?]*)#', $currentUrl, $match);
        return $match[1] ?? '';
    }

    private function returnJson(ResponseInterface $response): Json
    {
        return $this->jsonResultFactory->create()->setJsonData($response->getBody()->getContents());
    }
}
