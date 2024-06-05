<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\TestConnection;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Phrase;
use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Resource\AdapterFactory;
use Omikron\FactFinder\Communication\Version;
use Omikron\Factfinder\Logger\FactFinderLogger;
use Omikron\Factfinder\Model\Config\AuthConfig;
use Psr\Http\Client\ClientExceptionInterface;

class TestConnection extends Action
{
    private string $obscuredValue = '******';

    public function __construct(
        Action\Context $context,
        private readonly JsonFactory $jsonResultFactory,
        private readonly AuthConfig $authConfig,
        private readonly ClientBuilder $clientBuilder,
        private readonly FactFinderLogger $logger
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $request       = $this->getRequest();
            $clientBuilder = $this->clientBuilder
                ->withApiKey($this->getApiKey($this->getRequest()->getParams()))
                ->withServerUrl($request->getParam('address'));

            $adapterFactory = new AdapterFactory(
                $clientBuilder,
                Version::NG,
                'v5'
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

    private function getApiKey(array $params): string
    {
        // The password wasn't edited, load it from config
        if (!isset($params['ff_api_key']) || $params['ff_api_key'] === $this->obscuredValue) {
            $params['ff_api_key'] = $this->authConfig->getApiKey();
        }

        return $params['ff_api_key'];
    }
}
