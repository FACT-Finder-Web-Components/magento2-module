<?php

namespace Omikron\Factfinder\Test\Unit\Controller\Result;

use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Framework\View\Result\Page;
use Omikron\Factfinder\Controller\Result\Index;

class IndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Omikron\Factfinder\Controller\Result\Index
     */
    protected $index;

    /**
     * @var \Magento\Framework\View\Result\Page
     */
    protected $page;

    public function setUp()
    {
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->page = $this->getMockBuilder(Page::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resultPageFactory = $this->getMockBuilder(PageFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resultPageFactory->method('create')
            ->willReturn($this->page);

        $this->index = new Index($context, $resultPageFactory);
    }

    public function testExecute()
    {
        $this->assertInstanceOf(Page::class, $this->index->execute());
    }
}
