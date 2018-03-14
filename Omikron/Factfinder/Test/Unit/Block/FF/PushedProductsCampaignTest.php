<?php

namespace Omikron\Factfinder\Test\Unit\Block\FF;

use \Magento\Framework\View\Element\Template\Context;
use \Magento\Catalog\Helper\Image;
use Omikron\Factfinder\Block\FF\PushedProductsCampaign;

class PushedProductsCampaignTest extends \PHPUnit_Framework_TestCase
{
    public function testPushedProductsCampaign()
    {
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $image = $this->getMockBuilder(Image::class)
            ->disableOriginalConstructor()
            ->getMock();

        new PushedProductsCampaign($context, [], $image);
    }
}
