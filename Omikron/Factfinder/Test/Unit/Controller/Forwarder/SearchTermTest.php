<?php

namespace Omikron\Factfinder\Test\Unit\Controller\Forwarder;

use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\UrlInterface;
use \Magento\Framework\View\Result\Page;
use \Magento\Framework\App\ResponseInterface;
use Omikron\Factfinder\Controller\Forwarder\SearchTerm;
use Omikron\Factfinder\Helper\Data;

class SearchTermTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;

    /**
     * @var Omikron\Factfinder\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    public function setUp()
    {
        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->helper = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->scopeConfigInterface = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testExecute()
    {
        $url = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $url->method('getBaseUrl')
            ->willReturn('http://base.url');
        $response = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $response->method('sendResponse')
            ->willReturn(null);
        $this->context->method('getUrl')
            ->willReturn($url);
        $this->context->method('getResponse')
            ->willReturn($response);
        $this->helper->method('isEnabled')
            ->willReturn(true);

        $searchTerm = $this->getMockBuilder(SearchTerm::class)
            ->setMethods(array('_redirect'))
            ->setConstructorArgs(array($this->context, $this->scopeConfigInterface, $this->helper))
            ->getMock();
        $searchTerm->method('_redirect')
            ->willReturn($response);

        $this->assertNull($searchTerm->execute());
    }

    public function testExecuteWhenNotEnabled()
    {
        $resultPage = $this->getMockBuilder(Page::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resultFactory = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resultFactory->method('create')
            ->willReturn($resultPage);
        $this->context->method('getResultFactory')
            ->willReturn($resultFactory);

        $searchTerm = new SearchTerm($this->context, $this->scopeConfigInterface, $this->helper);

        $this->assertEquals($resultPage, $searchTerm->execute());
    }
}
