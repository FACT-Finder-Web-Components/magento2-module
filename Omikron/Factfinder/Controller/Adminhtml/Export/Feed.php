<?php

namespace Omikron\Factfinder\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\Store;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Model\Export\Feed as FeedGenerator;
use Omikron\Factfinder\Model\StoreEmulation;
use Omikron\Factfinder\Model\Stream\CsvFactory;

class Feed extends Action
{
    /** @var JsonFactory */
    private $jsonResultFactory;

    /** @var CommunicationConfigInterface */
    private $config;

    /** @var StoreEmulation */
    private $storeEmulation;

    /** @var FeedGenerator */
    private $feedGenerator;

    /** @var CsvFactory */
    private $csvFactory;

    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        CommunicationConfigInterface $config,
        StoreEmulation $storeEmulation,
        FeedGenerator $feedGenerator,
        CsvFactory $csvFactory
    ) {
        parent::__construct($context);
        $this->jsonResultFactory = $jsonResultFactory;
        $this->config            = $config;
        $this->storeEmulation    = $storeEmulation;
        $this->feedGenerator     = $feedGenerator;
        $this->csvFactory        = $csvFactory;
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();

        try {
            preg_match('@/store/([0-9]+)/@', (string) $this->_redirect->getRefererUrl(), $match);
            $this->storeEmulation->runInStore($match[1] ?? Store::DEFAULT_STORE_ID, function () {
                $channel  = $this->config->getChannel();
                $filename = "factfinder/export.{$channel}.csv";
                $this->feedGenerator->generate($this->csvFactory->create(['filename' => $filename]));
            });

            $result->setData(['message' => __('Feed successfully generated')]);
        } catch (\Exception $e) {
            $result->setData(['message' => $e->getMessage()]);
        }

        return $result;
    }
}
