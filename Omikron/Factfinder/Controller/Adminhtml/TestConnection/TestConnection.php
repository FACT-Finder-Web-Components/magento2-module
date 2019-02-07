<?php

declare(strict_types = 1);

namespace Omikron\Factfinder\Controller\Adminhtml\TestConnection;

use Magento\Framework\Registry;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Backend\App\Action\Context;
use Omikron\Factfinder\Exception\RequestException;
use Omikron\Factfinder\Model\Consumer\UpdateFieldRoles;

/**
 * Class TestConnection
 * Handles requests for Testing the connection to FACT-Finder
 */
class TestConnection extends \Magento\Backend\App\Action
{
    const OBSCURED_VALUE = '******';

    /** @var JsonFactory  */
    protected $resultJsonFactory;

    /** @var UpdateFieldRoles  */
    protected $updateFieldRoles;

    /** @var StoreManagerInterface  */
    protected $storeManager;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        StoreManagerInterface $storeManager,
        UpdateFieldRoles $updateFieldRoles
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->updateFieldRoles = $updateFieldRoles;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Gets called on request from test-connection button in the backend
     *
     * @return \Magento\Framework\Controller\Result\Json
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $authData = $this->getAuthFromRequest();

        // get current store view from HTTP_REFERER
        $result      = [];
        $httpReferer = $this->_redirect->getRefererUrl();

        if(isset($httpReferer)) {
            preg_match('@/store/([0-9]+)/@', $httpReferer, $result);
        }

        /** @var \Magento\Store\Api\Data\StoreInterface $store */
        if (isset($result[1])) {
            $store = $this->storeManager->getStore((int) $result[1]);
        } else {
            $store = $this->storeManager->getStore();
        }

        try {
            $conCheck = $this->updateFieldRoles->execute((int) $store->getId(), $authData);
            if ($conCheck['success']) {
                $message = __('Success! Connection successfully tested!');
            } else {
                $message = __('Error! Connection could not be established. Please check your setup.');
                if ($conCheck['ff_error_stacktrace'] ?? []) {
                    $message .= ' ' . __('FACT-Finder error message:') . ' ' . $conCheck['ff_error_stacktrace'];
                }
            }
        } catch (RequestException $e) {
            $message = $e->getMessage();
        }

        return $this->resultJsonFactory->create()->setData(['message' => $message]);
    }

    /**
     * @return array
     */
    private function getAuthFromRequest()
    {
        /** @var RequestInterface $request */
        $request = $this->getRequest();
        $password = $request->getParam('password');
        if ($password == self::OBSCURED_VALUE) {
            $password = null;
        }

        return [
            'serverUrl' => $request->getParam('serverUrl'),
            'channel' => $request->getParam('channel'),
            'password' => $password,
            'username' => $request->getParam('username'),
            'authenticationPrefix' => $request->getParam('authenticationPrefix'),
            'authenticationPostfix' => $request->getParam('authenticationPostfix'),
        ];
    }
}
