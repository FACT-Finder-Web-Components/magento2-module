<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\FieldRoles;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\Store;
use Omikron\Factfinder\Exception\ResponseException;
use Omikron\Factfinder\Model\Api\UpdateFieldRoles;

class Update extends Action
{
    /** @var UpdateFieldRoles  */
    private $updateFieldRoles;

    /** @var JsonFactory  */
    private $jsonResultFactory;

    public function __construct(Context $context, UpdateFieldRoles $updateFieldRoles, JsonFactory $jsonFactory)
    {
        parent::__construct($context);
        $this->updateFieldRoles  = $updateFieldRoles;
        $this->jsonResultFactory = $jsonFactory;
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        preg_match('@/store/([0-9]+)/@', (string) $this->_redirect->getRefererUrl(), $match);
        try {
            $this->updateFieldRoles->execute((int) $match[1] ?? Store::DEFAULT_STORE_ID);
            $result->setData(['message' => __('Field roles updated successfully')]);
        } catch (ResponseException $e) {
            $result->setData(['message' => $e->getMessage()]);
        }

        return $result;
    }
}
