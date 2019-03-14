<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Export;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Omikron\Factfinder\Api\Config\ChannelProviderInterface;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\StoreEmulation;
use Omikron\Factfinder\Model\Stream\CsvFactory;
use Magento\Store\Model\StoreManagerInterface;

class Product extends Action
{
    /** @var ChannelProviderInterface */
    private $channelProvider;

    /** @var StoreEmulation */
    private $storeEmulation;

    /** @var FeedGeneratorFactory */
    private $feedGeneratorFactory;

    /** @var FileFactory */
    private $fileFactory;

    /** @var CsvFactory */
    private $csvFactory;

    private $storeManager;

    /** @var string */
    protected $feedType = 'product';

    public function __construct(
        Context $context,
        ChannelProviderInterface $channelProvider,
        StoreEmulation $storeEmulation,
        FeedGeneratorFactory $feedGeneratorFactory,
        FileFactory $fileFactory,
        CsvFactory $csvFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->channelProvider      = $channelProvider;
        $this->storeEmulation       = $storeEmulation;
        $this->feedGeneratorFactory = $feedGeneratorFactory;
        $this->csvFactory           = $csvFactory;
        $this->fileFactory          = $fileFactory;
        $this->storeManager         = $storeManager;
    }

    public function execute()
    {
        $storeId = (int) $this->getRequest()->getParam('store', $this->storeManager->getDefaultStoreView()->getId());
        $this->storeEmulation->runInStore($storeId, function () {
            $filename = "export.{$this->channelProvider->getChannel()}.csv";
            $stream   = $this->csvFactory->create(['filename' => "factfinder/{$filename}"]);
            $this->feedGeneratorFactory->create($this->feedType)->generate($stream);
            $this->fileFactory->create($filename, $stream->getContent());
        });
    }
}
