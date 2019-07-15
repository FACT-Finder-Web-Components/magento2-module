<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Model\FeedServiceFactory;

class Feed extends Action
{
    /** @var JsonFactory */
    private $jsonResultFactory;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var FeedServiceFactory */
    private $feedServiceFactory;

    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        StoreManagerInterface $storeManager,
        FeedServiceFactory $feedServiceFactory
    ) {
        parent::__construct($context);
        $this->jsonResultFactory  = $jsonResultFactory;
        $this->storeManager       = $storeManager;
        $this->feedServiceFactory = $feedServiceFactory;
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        try {
            $type = $this->getRequest()->getParam('type', 'product');
            preg_match('@/store/([0-9]+)/@', (string) $this->_redirect->getRefererUrl(), $match);
            $storeId = (int) ($match[1] ?? $this->storeManager->getDefaultStoreView()->getId());
            $feedService = $this->feedServiceFactory->create($type);
            $feedService->integrate($storeId);
            $result->setData(['message' => __('Feed successfully generated')]);
        } catch (\Exception $e) {
            $result->setData(['message' => $e->getMessage()]);
        }

        return $result;
    }
}
