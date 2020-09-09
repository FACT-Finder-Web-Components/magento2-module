<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\FieldRoles;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Exception\ResponseException;
use Omikron\Factfinder\Model\Api\ActionFactory;

class Update extends Action
{
    /** @var ActionFactory */
    private $actionFactory;

    /** @var JsonFactory */
    private $jsonResultFactory;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    public function __construct(
        Context $context,
        ActionFactory $actionFactory,
        JsonFactory $jsonFactory,
        StoreManagerInterface $storeManager,
        CommunicationConfigInterface $communicationConfig
    ) {
        parent::__construct($context);
        $this->actionFactory     = $actionFactory;
        $this->jsonResultFactory   = $jsonFactory;
        $this->storeManager        = $storeManager;
        $this->communicationConfig = $communicationConfig;
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        preg_match('@/store/([0-9]+)/@', (string) $this->_redirect->getRefererUrl(), $match);
        try {
            $this->actionFactory
                ->withApiVersion($this->communicationConfig->getVersion())
                ->getUpdateFieldRoles()
                ->execute((int) ($match[1] ?? $this->storeManager->getDefaultStoreView()->getId()));
            $result->setData(['message' => __('Field roles updated successfully')]);
        } catch (ResponseException $e) {
            $result->setData(['message' => $e->getMessage()]);
        }

        return $result;
    }
}
