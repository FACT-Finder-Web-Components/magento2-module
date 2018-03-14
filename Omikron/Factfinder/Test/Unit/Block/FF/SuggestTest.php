<?php

namespace Omikron\Factfinder\Test\Unit\Block\FF;

use Omikron\Factfinder\Block\FF\Suggest;
use \Magento\Framework\View\Element\Template\Context;
use Omikron\Factfinder\Helper\Data;

class SuggestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Omikron\Factfinder\Block\FF\Suggest
     */
    protected $suggest;

    public function setUp()
    {
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $data = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->suggest = new Suggest($context, $data);
    }

    public function testToHtmlWhenGetFFSuggestIsFalse()
    {
        $this->assertEquals('', $this->suggest->toHtml());
        $this->assertInternalType('string', $this->suggest->toHtml());
    }
}
