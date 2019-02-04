<?php

namespace Omikron\Factfinder\Controller\Adminhtml\TestConnection;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;

/**
 * Class TestConnection
 * Handles requests for Testing the connection to FF
 *
 * @package Omikron\Factfinder\Controller\Adminhtml\TestConnection
 */
class TestConnection extends \Magento\Backend\App\Action
{
    /** @var \Magento\Framework\Controller\Result\JsonFactory */
    protected $_resultJsonFactory;

    /** @var \Omikron\Factfinder\Helper\Communication */
    protected $_communicationHelper;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var string
     */
    protected $obscuredValue = '******';

    /**
     * TestConnection constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Omikron\Factfinder\Helper\Communication $communication
     * @param Registry $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omikron\Factfinder\Helper\Communication $communication,
        Registry $registry
    )
    {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_communicationHelper = $communication;
        $this->_storeManager = $storeManager;
        $this->registry = $registry;
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
        $this->registry->register('ff-auth', $authData, true);

        // get current store view from HTTP_REFERER
        $result = [];
        $httpReferer = $this->_redirect->getRefererUrl();

        if(isset($httpReferer)) {
            preg_match('@/store/([0-9]+)/@', $httpReferer, $result);
        }

        /** @var \Magento\Store\Api\Data\StoreInterface $store */
        if (isset($result[1])) {
            $store = $this->_storeManager->getStore((int) $result[1]);
        } else {
            $store = $this->_storeManager->getStore();
        }

        $conCheck = $this->_communicationHelper->checkConnection($store);
        if ($conCheck['success']) {
            $message = (string) __('Success! Connection successfully tested!');
        } else {
            $message = (string) __('Error! Connection could not be established. Please check your setup.');
            if(strlen($conCheck['ff_error_stacktrace'])) {
                $message .= ' ' . __('FACT-Finder error message:') . ' ' . $conCheck['ff_error_stacktrace'];
            }
        }
        return $this->_resultJsonFactory->create()->setData(['message' => $message]);
    }

    /**
     * @return array
     */
    private function getAuthFromRequest()
    {
        /** @var RequestInterface $request */
        $request = $this->getRequest();
        $password = $request->getParam('password');
        if ($password == $this->obscuredValue) {
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
