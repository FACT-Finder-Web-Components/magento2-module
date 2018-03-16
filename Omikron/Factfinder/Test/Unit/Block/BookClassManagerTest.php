<?php

namespace Omikron\Factfinder\Test\Unit\Block;

use \Magento\Framework\View\Element\BlockInterface;
use \Magento\Framework\View\Element\Template\Context;
use \Magento\Framework\View\LayoutInterface;
use Omikron\Factfinder\Block\BlockClassManager;

class BookClassManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testSetBlockSearchbox()
    {
        $blockInterface = $this->getMockBuilder(BlockInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $layout = $this->getMockBuilder(LayoutInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $layout->method('unsetElement')
            ->withAnyParameters()
            ->willReturn($layout);
        $layout->method('createBlock')
            ->willReturn($blockInterface);
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bookClassManager = $this->getMockBuilder(BlockClassManager::class)
            ->setMethods(array('getLayout'))
            ->setConstructorArgs(array($context))
            ->getMock();
        $bookClassManager->method('getLayout')
            ->willReturn($layout);


        $this->assertNull($bookClassManager->setBlockSearchbox('Searchbox'));
    }
}
