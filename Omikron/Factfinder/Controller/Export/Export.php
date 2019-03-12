<?php

namespace Omikron\Factfinder\Controller\Export;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\Store;
use Omikron\Factfinder\Api\Config\ChannelProviderInterface;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\StoreEmulation;
use Omikron\Factfinder\Model\Stream\Browser;

class Export extends Action
{
    /** @var ChannelProviderInterface */
    private $channelProvider;

    /** @var StoreEmulation */
    private $storeEmulation;

    /** @var FeedGeneratorFactory */
    private $feedGeneratorFactory;

    /** @var string */
    protected $feedType = 'product';

    public function __construct(
        Context $context,
        ChannelProviderInterface $channelProvider,
        StoreEmulation $storeEmulation,
        FeedGeneratorFactory $feedGeneratorFactory
    ) {
        parent::__construct($context);
        $this->channelProvider      = $channelProvider;
        $this->storeEmulation       = $storeEmulation;
        $this->feedGeneratorFactory = $feedGeneratorFactory;
    }

    public function execute()
    {
        preg_match('@/store/([0-9]+)@', (string) $this->getRequest()->getPathInfo(), $match);
        $this->storeEmulation->runInStore($match[1] ?? Store::DEFAULT_STORE_ID, function () {
            $feedGenerator = $this->feedGeneratorFactory->create($this->feedType);
            $feedGenerator->generate(new Browser("factfinder/export.{$this->channelProvider->getChannel()}.csv"));
        });
    }
}
