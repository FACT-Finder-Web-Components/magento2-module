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
    /** @var \Magento\Framework\Controller\Result\JsonFactory */
    protected $resultJsonFactory;

    /** @var \Omikron\Factfinder\Model\Export\Product */
    protected $productExporter;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omikron\Factfinder\Model\Export\Product $productExporter
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager      = $storeManager;
        $this->productExporter   = $productExporter;
    }

    /**
     * Get called through export button in the backend
     *
     * @return \Magento\Framework\Controller\Result\Json
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        // get current store view from HTTP_REFERER
        preg_match('@/store/([0-9]+)/@', (string) $this->_redirect->getRefererUrl(), $result);
        $store  = $this->storeManager->getStore($result[1] ?? null);
        $result = $this->productExporter->exportProduct($store);
        return $this->resultJsonFactory->create()->setData($result);
    }
}
