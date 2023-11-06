<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\TestConnection;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Phrase;
use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Credentials;
use Omikron\FactFinder\Communication\Resource\AdapterFactory;
use Omikron\Factfinder\Logger\FactFinderLogger;
use Omikron\Factfinder\Model\Api\CredentialsFactory;
use Omikron\Factfinder\Model\Config\AuthConfig;
use Psr\Http\Client\ClientExceptionInterface;

class TestConnection extends Action
{
    private string $obscuredValue = '******';
    private JsonFactory $jsonResultFactory;
    private CredentialsFactory $credentialsFactory;
    private AuthConfig $authConfig;
    private ClientBuilder $clientBuilder;
    private FactFinderLogger $logger;

    public function __construct(
        Action\Context $context,
        JsonFactory $jsonResultFactory,
        CredentialsFactory $credentialsFactory,
        AuthConfig $authConfig,
        ClientBuilder $clientBuilder,
        FactFinderLogger $logger
    ) {
        parent::__construct($context);
        $this->jsonResultFactory  = $jsonResultFactory;
        $this->credentialsFactory = $credentialsFactory;
        $this->authConfig         = $authConfig;
        $this->clientBuilder      = $clientBuilder;
        $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $request       = $this->getRequest();
            $clientBuilder = $this->clientBuilder
                ->withCredentials($this->getCredentials($this->getRequest()->getParams()))
                ->withServerUrl($request->getParam('address'));

            $adapterFactory = new AdapterFactory(
                $clientBuilder,
                $request->getParam('version'),
                $request->getParam('ff_api_version')
            );
            $searchAdapter = $adapterFactory->getSearchAdapter();
            $searchAdapter->search($request->getParam('channel'), '*');

            $message = new Phrase('Connection successfully established.');
        } catch (ClientExceptionInterface $e) {
            $this->logger->error(new Phrase(
                'FACT-Finder response exception: %1, thrown at %2',
                [$e->getMessage(), $e->getTraceAsString()]
            ));
            $message = $e->getMessage();
        }

        return $this->jsonResultFactory->create()->setData(['message' => $message]);
    }

    private function getCredentials(array $params): Credentials
    {
        // The password wasn't edited, load it from config
        if (!isset($params['password']) || $params['password'] === $this->obscuredValue) {
            $params['password'] = $this->authConfig->getPassword();
        }

        return $this->credentialsFactory->create($params);
    }
}
