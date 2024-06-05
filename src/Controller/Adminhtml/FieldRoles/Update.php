<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\FieldRoles;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Resource\AdapterFactory;
use Omikron\Factfinder\Model\Config\AuthConfig;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\FieldRoles;
use Psr\Http\Client\ClientExceptionInterface;

class Update extends Action
{
    public function __construct(
        Context $context,
        private readonly JsonFactory $jsonFactory,
        private readonly StoreManagerInterface $storeManager,
        private readonly CommunicationConfig $communicationConfig,
        private readonly AuthConfig $authConfig,
        private readonly FieldRoles $fieldRoles,
        private readonly ClientBuilder $clientBuilder
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();
        try {
            //@phpcs:ignore Magento2.Legacy.ObsoleteResponse.RedirectResponseMethodFound
            preg_match('@/store/([0-9]+)/@', (string) $this->_redirect->getRefererUrl(), $match);
            $storeId = (int) ($match[1] ?? $this->storeManager->getDefaultStoreView()->getId());
            $client  = $this->clientBuilder
                ->withApiKey($this->authConfig->getApiKey())
                ->withServerUrl($this->communicationConfig->getAddress());

            $adapterFactory = new AdapterFactory(
                $client,
                $this->communicationConfig->getVersion(),
                $this->communicationConfig->getApiVersion()
            );
            $searchAdapter = $adapterFactory->getSearchAdapter();
            $searchResult  = $searchAdapter->search($this->communicationConfig->getChannel($storeId), 'Search.ff');
            $result->setData(['message' => __('Search result does not contain field roles')]);

            if (isset($searchResult['fieldRoles'])) {
                $this->fieldRoles->saveFieldRoles($searchResult['fieldRoles'], $storeId);
                $result->setData(['message' => __('Field roles updated successfully')]);
            }

        } catch (ClientExceptionInterface $e) {
            $result->setData(['message' => $e->getMessage()]);
        }

        return $result;
    }
}
