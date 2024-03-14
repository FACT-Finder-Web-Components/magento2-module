<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Export;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\StreamInterfaceFactory;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\StoreEmulation;
use Omikron\Factfinder\Service\FeedFileService;

class Product extends Action
{
    protected string $feedType = 'product';

    public function __construct(
        Context                                 $context,
        private readonly CommunicationConfig    $communicationConfig,
        private readonly StoreEmulation         $storeEmulation,
        private readonly FeedGeneratorFactory   $feedGeneratorFactory,
        private readonly FileFactory            $fileFactory,
        private readonly StreamInterfaceFactory $streamFactory,
        private readonly StoreManagerInterface  $storeManager,
        private readonly FeedFileService $feedFileService
    ) {
        parent::__construct($context);
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
