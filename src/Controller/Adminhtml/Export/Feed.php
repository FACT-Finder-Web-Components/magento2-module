<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Model\Api\ActionFactory;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\FtpUploader;
use Omikron\Factfinder\Model\StoreEmulation;
use Omikron\Factfinder\Model\Stream\CsvFactory;

class Feed extends Action
{
    /** @var JsonFactory */
    private $jsonResultFactory;

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    /** @var StoreEmulation */
    private $storeEmulation;

    /** @var FeedGeneratorFactory */
    private $feedGeneratorFactory;

    /** @var CsvFactory */
    private $csvFactory;

    /** @var FtpUploader */
    private $ftpUploader;

    /** @var ActionFactory  */
    private $actionFactory;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var string */
    protected $feedType = 'product';

    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        CommunicationConfigInterface $communicationConfig,
        StoreEmulation $storeEmulation,
        FeedGeneratorFactory $feedGeneratorFactory,
        CsvFactory $csvFactory,
        FtpUploader $ftpUploader,
        ActionFactory $actionFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->jsonResultFactory    = $jsonResultFactory;
        $this->communicationConfig  = $communicationConfig;
        $this->storeEmulation       = $storeEmulation;
        $this->feedGeneratorFactory = $feedGeneratorFactory;
        $this->csvFactory           = $csvFactory;
        $this->ftpUploader          = $ftpUploader;
        $this->actionFactory      = $actionFactory;
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
                $this->actionFactory->withApiVersion($this->communicationConfig->getVersion())
                    ->getPushImport()->execute($storeId);
            });

            $result->setData(['message' => __('Feed successfully generated')]);
        } catch (\Exception $e) {
            $result->setData(['message' => $e->getMessage()]);
        }

        return $result;
    }
}
