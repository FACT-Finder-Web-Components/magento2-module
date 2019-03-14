<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\FieldRoles;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Omikron\Factfinder\Exception\ResponseException;
use Omikron\Factfinder\Model\Api\UpdateFieldRoles;
use Magento\Store\Model\StoreManagerInterface;

class Update extends Action
{
    /** @var UpdateFieldRoles  */
    private $updateFieldRoles;

    /** @var JsonFactory  */
    private $jsonResultFactory;

    private $storeManager;

    public function __construct(
        Context $context,
        UpdateFieldRoles $updateFieldRoles,
        JsonFactory $jsonFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->updateFieldRoles  = $updateFieldRoles;
        $this->jsonResultFactory = $jsonFactory;
        $this->storeManager      = $storeManager;
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        preg_match('@/store/([0-9]+)/@', (string) $this->_redirect->getRefererUrl(), $match);
        try {
            $this->updateFieldRoles->execute((int) ($match[1] ?? $this->storeManager->getDefaultStoreView()->getId()));
            $result->setData(['message' => __('Field roles updated successfully')]);
        } catch (ResponseException $e) {
            $result->setData(['message' => $e->getMessage()]);
        }

        return $result;
    }
}
