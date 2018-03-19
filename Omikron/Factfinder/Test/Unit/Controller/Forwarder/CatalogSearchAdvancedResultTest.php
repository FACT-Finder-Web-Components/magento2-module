<?php

namespace Omikron\Factfinder\Test\Unit\Controller\Forwarder;

use \Magento\Framework\App\Action\Context;
use \Magento\CatalogSearch\Model\Advanced;
use \Magento\Framework\UrlFactory;
use \Magento\Framework\App\ViewInterface;
use \Magento\Framework\App\Request\Http\Proxy;
use \Magento\Framework\UrlInterface;
use \Magento\Framework\App\ResponseInterface;
use Omikron\Factfinder\Controller\Forwarder\CatalogSearchAdvancedResult;
use Omikron\Factfinder\Helper\Data;

class CatalogSearchAdvancedResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Request\Http\Proxy
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\CatalogSearch\Model\Advanced
     */
    protected $catalogSearchAdvanced;

    /**
     * @var \Magento\Framework\UrlFactory
     */
    protected $urlFactory;

    /**
     * @var Omikron\Factfinder\Helper\Data
     */
    protected $helper;

    public function setUp()
    {
        $this->request = $this->getMockBuilder(Proxy::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->context->method('getRequest')
            ->willReturn($this->request);
        $this->catalogSearchAdvanced = $this->getMockBuilder(Advanced::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->urlFactory = $this->getMockBuilder(UrlFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->helper = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testExecute()
    {
        $url = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
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
        $this->helper->method('getDefaultQuery')
            ->willReturn('*');
        $this->request->method('getParam')
            ->withAnyParameters()
            ->willReturn('query');

        $catalogSearchAdvancedResultTest = $this->getMockBuilder(CatalogSearchAdvancedResult::class)
            ->setConstructorArgs(array($this->context, $this->catalogSearchAdvanced, $this->urlFactory, $this->helper))
            ->setMethods(array('_redirect'))
            ->getMock();
        $catalogSearchAdvancedResultTest->method('_redirect')
            ->withAnyParameters()
            ->willReturn($response);

        $this->assertNull($catalogSearchAdvancedResultTest->execute());
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
        $this->request->method('getQueryValue')
            ->willReturn(null);
        $this->context->method('getView')
            ->willReturn($view);
        $this->catalogSearchAdvanced->method('addFilters')
            ->withAnyParameters()
            ->willReturn($this->catalogSearchAdvanced);

        $catalogSearchAdvancedResult = new CatalogSearchAdvancedResult($this->context, $this->catalogSearchAdvanced, $this->urlFactory, $this->helper);

        $this->assertNull($catalogSearchAdvancedResult->execute());
    }
}
