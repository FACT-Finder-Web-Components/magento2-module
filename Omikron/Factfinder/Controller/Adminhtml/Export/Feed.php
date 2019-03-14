<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\Config\ChannelProviderInterface;
use Omikron\Factfinder\Model\Api\PushImport;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\FtpUploader;
use Omikron\Factfinder\Model\StoreEmulation;
use Omikron\Factfinder\Model\Stream\CsvFactory;

class Feed extends Action
{
    /** @var JsonFactory */
    private $jsonResultFactory;

    /** @var ChannelProviderInterface */
    private $channelProvider;

    /** @var StoreEmulation */
    private $storeEmulation;

    /** @var FeedGeneratorFactory */
    private $feedGeneratorFactory;

    /** @var CsvFactory */
    private $csvFactory;

    /** @var FtpUploader */
    private $ftpUploader;

    /** @var PushImport */
    private $pushImport;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var string */
    protected $feedType = 'product';

    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        ChannelProviderInterface $channelProvider,
        StoreEmulation $storeEmulation,
        FeedGeneratorFactory $feedGeneratorFactory,
        CsvFactory $csvFactory,
        FtpUploader $ftpUploader,
        PushImport $pushImport,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->jsonResultFactory    = $jsonResultFactory;
        $this->channelProvider      = $channelProvider;
        $this->storeEmulation       = $storeEmulation;
        $this->feedGeneratorFactory = $feedGeneratorFactory;
        $this->csvFactory           = $csvFactory;
        $this->ftpUploader          = $ftpUploader;
        $this->pushImport           = $pushImport;
        $this->storeManager         = $storeManager;
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();

        try {
            preg_match('@/store/([0-9]+)/@', (string) $this->_redirect->getRefererUrl(), $match);
            $storeId = (int) ($match[1] ?? $this->storeManager->getDefaultStoreView()->getId());
            $this->storeEmulation->runInStore($storeId, function () use ($storeId) {
                $filename = "export.{$this->channelProvider->getChannel()}.csv";
                $stream   = $this->csvFactory->create(['filename' => "factfinder/{$filename}"]);
                $this->feedGeneratorFactory->create($this->feedType)->generate($stream);
                $this->ftpUploader->upload($filename, $stream);
                $this->pushImport->execute($storeId);
            });

            $result->setData(['message' => __('Feed successfully generated')]);
        } catch (\Exception $e) {
            $result->setData(['message' => $e->getMessage()]);
        }

        return $result;
    }
}
