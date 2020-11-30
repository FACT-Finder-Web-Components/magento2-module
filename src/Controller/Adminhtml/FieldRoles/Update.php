<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\FieldRoles;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\FactFinder\Communication\Exception\ResponseException;
use Omikron\FactFinder\Communication\Resource\Builder;
use Omikron\Factfinder\Model\Api\CredentialsFactory;
use Omikron\Factfinder\Model\FieldRoles;

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

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        StoreManagerInterface $storeManager,
        CommunicationConfigInterface $communicationConfig,
        CredentialsFactory $credentialsFactory,
        FieldRoles $fieldRoles
    ) {
        parent::__construct($context);
        $this->jsonResultFactory   = $jsonFactory;
        $this->storeManager        = $storeManager;
        $this->communicationConfig = $communicationConfig;
        $this->credentialsFactory  = $credentialsFactory;
        $this->fieldRoles          = $fieldRoles;
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        try {
            preg_match('@/store/([0-9]+)/@', (string)$this->_redirect->getRefererUrl(), $match);
            $storeId = (int) ($match[1] ?? $this->storeManager->getDefaultStoreView()->getId());
            $resource = (new Builder())
                ->withCredentials($this->credentialsFactory->create())
                ->withApiVersion($this->communicationConfig->getVersion())
                ->withServerUrl($this->communicationConfig->getAddress())
                ->build();

            $response = $resource->search('Search.ff', $this->communicationConfig->getChannel($storeId));
            $this->fieldRoles->saveFieldRoles($response['fieldRoles'], $storeId);

            $result->setData(['message' => __('Field roles updated successfully')]);
        } catch (ResponseException $e) {
            $result->setData(['message' => $e->getMessage()]);
        }

        return $result;
    }
}
