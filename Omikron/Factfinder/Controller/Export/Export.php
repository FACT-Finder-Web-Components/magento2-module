<?php

namespace Omikron\Factfinder\Controller\Export;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\Config\ChannelProviderInterface;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\Export\Product as ProductExport;
use Omikron\Factfinder\Model\StoreEmulation;
use Omikron\Factfinder\Model\Stream\BrowserFactory;

class Export extends Action
{
    /** @var ProductExport */
    protected $productExport;

    /** @var ChannelProviderInterface */
    private $channelProvider;

    /** @var StoreEmulation */
    private $storeEmulation;

    /** @var FeedGeneratorFactory */
    private $feedGeneratorFactory;

    /** @var BrowserFactory */
    private $browserFactory;

    /** @var string */
    protected $feedType = 'product';

    public function __construct(
        Context $context,
        ProductExport $productExport,
        ChannelProviderInterface $channelProvider,
        StoreEmulation $storeEmulation,
        FeedGeneratorFactory $feedGeneratorFactory,
        BrowserFactory $browserFactory
    ) {
        parent::__construct($context);
        $this->productExport        = $productExport;
        $this->channelProvider      = $channelProvider;
        $this->storeEmulation       = $storeEmulation;
        $this->feedGeneratorFactory = $feedGeneratorFactory;
        $this->browserFactory       = $browserFactory;
    }

    public function execute()
    {
        preg_match('@/store/([0-9]+)/@', (string) $this->_redirect->getRefererUrl(), $match);
        $this->storeEmulation->runInStore($match[1] ?? Store::DEFAULT_STORE_ID, function () {
            $channel       = $this->channelProvider->getChannel();
            $feedGenerator = $this->feedGeneratorFactory->create($this->feedType);
            $output        = $this->browserFactory->create(['fileName ' => "factfinder/export.{$channel}.csv"]);
            $feedGenerator->generate($output);
        });
    }
}
