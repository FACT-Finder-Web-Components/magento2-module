<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Export;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Model\FeedServiceFactory;
use Omikron\Factfinder\Model\Stream\CsvFactory;

class Feed extends Action
{
    /** @var FeedServiceFactory */
    private $feedServiceFactory;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var FileFactory */
    private $fileFactory;

    public function __construct(
        Context $context,
        FeedServiceFactory $feedServiceFactory,
        FileFactory $fileFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->feedServiceFactory = $feedServiceFactory;
        $this->fileFactory        = $fileFactory;
        $this->storeManager       = $storeManager;
    }

    public function execute()
    {
        $storeId = (int)$this->getRequest()->getParam('store', $this->storeManager->getDefaultStoreView()->getId());
        try {
            $type = $this->getRequest()->getParam('type', $this->storeManager->getDefaultStoreView()->getId());
            $feedService = $this->feedServiceFactory = $this->feedServiceFactory->create($type);
            $feed = $feedService->get($storeId);
            return $this->fileFactory->create($feed->getFileName(), $feed->getContent());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e);
            return $this->_forward('noroute');
        }
    }
}
