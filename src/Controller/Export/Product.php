<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Export;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\StoreEmulation;
use Omikron\Factfinder\Api\StreamInterfaceFactory;
use Omikron\Factfinder\Service\FeedFileService;

class Product extends Action
{
    protected string $feedType = 'product';
    private CommunicationConfig $communicationConfig;
    private StoreEmulation $storeEmulation;
    private FeedGeneratorFactory $feedGeneratorFactory;
    private FileFactory $fileFactory;
    private StreamInterfaceFactory $streamFactory;
    private StoreManagerInterface $storeManager;
    private FeedFileService $feedFileService;

    public function __construct(
        Context $context,
        CommunicationConfig $communicationConfig,
        StoreEmulation $storeEmulation,
        FeedGeneratorFactory $feedGeneratorFactory,
        FileFactory $fileFactory,
        StreamInterfaceFactory $streamFactory,
        StoreManagerInterface $storeManager,
        FeedFileService $feedFileService
    ) {
        parent::__construct($context);
        $this->communicationConfig  = $communicationConfig;
        $this->storeEmulation       = $storeEmulation;
        $this->feedGeneratorFactory = $feedGeneratorFactory;
        $this->streamFactory        = $streamFactory;
        $this->fileFactory          = $fileFactory;
        $this->storeManager         = $storeManager;
        $this->feedFileService      = $feedFileService;
    }

    public function execute()
    {
        $storeId = (int) $this->getRequest()->getParam('store', $this->storeManager->getDefaultStoreView()->getId());
        $this->storeEmulation->runInStore($storeId, function () {
            $filename = $this->feedFileService->getFeedExportFilename($this->feedType, $this->communicationConfig->getChannel());
            $stream   = $this->streamFactory->create(['filename' => "factfinder/{$filename}"]);
            $this->feedGeneratorFactory->create($this->feedType)->generate($stream);
            $this->fileFactory->create($filename, $stream->getContent());
        });
    }
}
