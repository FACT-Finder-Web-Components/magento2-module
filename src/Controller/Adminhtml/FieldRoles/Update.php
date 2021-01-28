<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\FieldRoles;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Resource\AdapterFactory;
use Omikron\Factfinder\Model\Api\CredentialsFactory;
use Omikron\Factfinder\Model\FieldRoles;
use Psr\Http\Client\ClientExceptionInterface;

class Update extends Action
{
    /** @var JsonFactory */
    private $jsonResultFactory;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    /** @var CredentialsFactory */
    private $credentialsFactory;

    /** @var FieldRoles */
    private $fieldRoles;

    /** @var ClientBuilder */
    private $clientBuilder;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        StoreManagerInterface $storeManager,
        CommunicationConfigInterface $communicationConfig,
        CredentialsFactory $credentialsFactory,
        FieldRoles $fieldRoles,
        ClientBuilder $clientBuilder
    ) {
        parent::__construct($context);
        $this->jsonResultFactory   = $jsonFactory;
        $this->storeManager        = $storeManager;
        $this->communicationConfig = $communicationConfig;
        $this->credentialsFactory  = $credentialsFactory;
        $this->fieldRoles          = $fieldRoles;
        $this->clientBuilder       = $clientBuilder;
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        try {
            preg_match('@/store/([0-9]+)/@', (string) $this->_redirect->getRefererUrl(), $match);
            $storeId = (int) ($match[1] ?? $this->storeManager->getDefaultStoreView()->getId());
            $client  = $this->clientBuilder
                ->withCredentials($this->credentialsFactory->create())
                ->withServerUrl($this->communicationConfig->getAddress());

            $searchAdapter = (new AdapterFactory($client, $this->communicationConfig->getVersion()))->getSearchAdapter();
            $response      = $searchAdapter->search($this->communicationConfig->getChannel($storeId), 'Search.ff');

            $this->fieldRoles->saveFieldRoles($response['fieldRoles'], $storeId);
            $result->setData(['message' => __('Field roles updated successfully')]);
        } catch (ClientExceptionInterface $e) {
            $result->setData(['message' => $e->getMessage()]);
        }

        return $result;
    }
}
