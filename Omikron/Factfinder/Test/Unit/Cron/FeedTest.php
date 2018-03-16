<?php

namespace Omikron\Factfinder\Test\Unit\Cron;

use \Magento\Framework\App\Config\ScopeConfigInterface;
use Omikron\Factfinder\Model\Export\Product;
use Omikron\Factfinder\Cron\Feed;

class FeedTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $productExport = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productExport->method('exportProducts')
            ->with($this->equalTo(true))
            ->willReturn([]);
        $scopeConfig = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $scopeConfig->method('getValue')
            ->with($this->equalTo('factfinder/data_transfer/ff_cron_enabled'))
            ->willReturn(true);

        $feed = new Feed($productExport, $scopeConfig);

        $this->assertNull($feed->execute());
    }
}
