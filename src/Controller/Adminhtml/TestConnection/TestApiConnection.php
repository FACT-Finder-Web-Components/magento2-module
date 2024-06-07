<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\TestConnection;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Phrase;
use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Credentials;
use Omikron\FactFinder\Communication\Resource\AdapterFactory;
use Omikron\FactFinder\Communication\Version;
use Omikron\Factfinder\Logger\FactFinderLogger;
use Omikron\Factfinder\Model\Api\CredentialsFactory;
use Omikron\Factfinder\Model\Config\AuthConfig;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Psr\Http\Client\ClientExceptionInterface;

class TestApiConnection extends Action
{
    private string $obscuredValue = '******';

    public function __construct(
        Action\Context $context,
        private readonly JsonFactory $jsonResultFactory,
        private readonly CredentialsFactory $credentialsFactory,
        private readonly AuthConfig $authConfig,
        private readonly CommunicationConfig $communicationConfig,
        private readonly ClientBuilder $clientBuilder,
        private readonly FactFinderLogger $logger
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $clientBuilder = $this->clientBuilder
                ->withCredentials($this->getCredentials($this->getRequest()->getParams()))
                ->withServerUrl($this->communicationConfig->getAddress());

            $adapterFactory = new AdapterFactory(
                $clientBuilder,
                Version::NG,
                'v5'
            );
            $searchAdapter = $adapterFactory->getSearchAdapter();
            $searchAdapter->search($this->communicationConfig->getChannel(), '*');

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
        if (!isset($params['ff_password']) || $params['ff_password'] === $this->obscuredValue) {
            $params['ff_password'] = $this->authConfig->getPassword();
        }

        return $this->credentialsFactory->create([
            'username' => $params['ff_username'],
            'password' => $params['ff_password'],
        ]);
    }
}
