<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\Store;
use Omikron\Factfinder\Api\Config\ChannelProviderInterface;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\StoreEmulation;
use Omikron\Factfinder\Model\Stream\FtpFactory;

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

    /** @var FtpFactory */
    private $ftpFactory;

    /** @var string */
    protected $feedType = 'product';

    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        ChannelProviderInterface $channelProvider,
        StoreEmulation $storeEmulation,
        FeedGeneratorFactory $feedGeneratorFactory,
        FtpFactory $ftpFactory
    ) {
        parent::__construct($context);
        $this->jsonResultFactory    = $jsonResultFactory;
        $this->channelProvider      = $channelProvider;
        $this->storeEmulation       = $storeEmulation;
        $this->feedGeneratorFactory = $feedGeneratorFactory;
        $this->ftpFactory           = $ftpFactory;
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();

        try {
            preg_match('@/store/([0-9]+)/@', (string) $this->_redirect->getRefererUrl(), $match);
            $this->storeEmulation->runInStore((int) ($match[1] ?? Store::DEFAULT_STORE_ID), function () {
                $channel       = $this->channelProvider->getChannel();
                $filename      = "factfinder/export.{$channel}.csv";
                $feedGenerator = $this->feedGeneratorFactory->create($this->feedType);
                $feedGenerator->generate($this->ftpFactory->create(['filename' => $filename]));
            });

            $result->setData(['message' => __('Feed successfully generated')]);
        } catch (\Exception $e) {
            $result->setData(['message' => $e->getMessage()]);
        }

        return $result;
    }
}
