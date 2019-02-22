<?php

namespace Omikron\Factfinder\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Model\Export\Product;

class Feed extends Action
{
    /** @var JsonFactory */
    protected $resultJsonFactory;

    /** @var Product */
    protected $productExporter;

    /** @var StoreManagerInterface */
    protected $storeManager;

    public function __construct(
        Action\Context $context,
        JsonFactory $resultJsonFactory,
        StoreManagerInterface $storeManager,
        Product $productExporter
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
