<?php

namespace Omikron\Factfinder\Controller\Adminhtml\TestConnection;

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
     * TestConnection constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Omikron\Factfinder\Helper\Communication $communication
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omikron\Factfinder\Helper\Communication $communication
    )
    {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_communicationHelper = $communication;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Gets called on request from test-connection button in the backend
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        // get current store view from HTTP_REFERER
        $result = [];
        $httpReferer = $this->_redirect->getRefererUrl();

        if(isset($httpReferer)) {
            preg_match('@/store/([0-9]+)/@', $httpReferer, $result);
        }

        /** @var \Magento\Store\Api\Data\StoreInterface $store */
        if (isset($result[1])) {
            $store = $this->_storeManager->getStore((int)$result[1]);
        } else {
            $store = $this->_storeManager->getStore();
        }

        $conCheck = $this->_communicationHelper->updateFieldRoles($store);
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
}
