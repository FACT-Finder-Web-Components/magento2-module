<?php

namespace Omikron\Factfinder\Controller\Adminhtml\Export;

/**
 * Class Feed
 * Used to handle manual export requests
 *
 * @package Omikron\Factfinder\Controller\Adminhtml\Export
 */
class Feed extends \Magento\Backend\App\Action
{
    /** @var \Magento\Framework\View\Result\PageFactory */
    protected $resultPageFactory;

    /** @var \Magento\Framework\Controller\Result\JsonFactory */
    protected $resultJsonFactory;

    /** @var \Omikron\Factfinder\Model\Export\Product */
    protected $productExporter;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * Feed constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Omikron\Factfinder\Model\Export\Product $productExporter
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omikron\Factfinder\Model\Export\Product $productExporter
    )
    {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_storeManager = $storeManager;
        $this->productExporter = $productExporter;
    }

    /**
     * Get called through export button in the backend
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        // get current store view from HTTP_REFERER
        $result = [];
        $httpReferer = $this->_redirect->getRefererUrl();

        if (isset($httpReferer)) {
            preg_match('@/store/([0-9]+)/@', $httpReferer, $result);
        }

        /** @var \Magento\Store\Api\Data\StoreInterface $store */
        if (isset($result[1])) {
            $store = $this->_storeManager->getStore((int) $result[1]);
        } else {
            $store = $this->_storeManager->getStore();
        }

        $result = $this->productExporter->exportProduct($store);

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($result);
    }
}
