<?php

namespace Omikron\Factfinder\Test\Unit\Controller\Forwarder;

use \Magento\Framework\App\Action\Context;
use \Magento\Framework\App\ViewInterface;
use \Magento\Framework\App\RequestInterface;
use \Magento\Framework\UrlInterface;
use \Magento\Framework\App\ResponseInterface;
use Omikron\Factfinder\Controller\Forwarder\CatalogSearchAdvanced;
use Omikron\Factfinder\Helper\Data;

class CatalogSearchAdvancedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;

    /**
     * @var Omikron\Factfinder\Helper\Data
     */
    protected $helper;

    public function setUp()
    {
        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->helper = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testExecute()
    {
        $this->helper->method('isEnabled')
            ->willReturn(true);
        $request = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->method('getParam')
            ->withAnyParameters()
            ->willReturn('query');
        $url = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $url->method('getBaseUrl')
            ->willReturn('http://example.com');
        $response = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $response->method('sendResponse')
            ->willReturn(null);
        $this->context->method('getRequest')
            ->willReturn($request);
        $this->context->method('getUrl')
            ->willReturn($url);
        $this->context->method('getResponse')
            ->willReturn($response);

        $catalogSearchAdvanced = $this->getMockBuilder(CatalogSearchAdvanced::class)
            ->setMethods(array('_redirect'))
            ->setConstructorArgs(array($this->context, $this->helper))
            ->getMock();
        $catalogSearchAdvanced->method('_redirect')
            ->willReturn($response);

        $this->assertNull($catalogSearchAdvanced->execute());
    }

    public function testExecuteWhenNotEnabled()
    {
        $view = $this->getMockBuilder(ViewInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $view->method('loadLayout')
            ->willReturn($view);
        $view->method('renderLayout')
            ->willReturn($view);
        $this->context->method('getView')
            ->willReturn($view);

        $catalogSearchAdvanced = new CatalogSearchAdvanced($this->context, $this->helper);

        $this->assertNull($catalogSearchAdvanced->execute());
    }
}
