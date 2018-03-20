<?php

namespace Omikron\Factfinder\Test\Unit\Controller\Forwarder;

use \Magento\Framework\App\Action\Context;
use \Magento\Catalog\Model\Session;
use \Magento\Framework\HTTP\PhpEnvironment\Response;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Store\Api\Data\StoreInterface;
use \Magento\Framework\App\Response\RedirectInterface;
use \Magento\Search\Model\QueryFactory;
use \Magento\Search\Model\Query;
use \Magento\Catalog\Model\Layer\Resolver;
use \Magento\Framework\App\RequestInterface;
use \Magento\Framework\UrlInterface;
use Omikron\Factfinder\Controller\Forwarder\CatalogSearchResult;
use Omikron\Factfinder\Helper\Data;

class CatalogSearchResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManagerInterface;

    /**
     * @var \Magento\Search\Model\QueryFactory
     */
    protected $queryFactory;

    /**
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    protected $resolver;

    /**
     * @var Omikron\Factfinder\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\Response
     */
    protected $response;

    public function setUp()
    {
        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->session = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManagerInterface = $this->getMockBuilder(StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->queryFactory = $this->getMockBuilder(QueryFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resolver = $this->getMockBuilder(Resolver::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->helper = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testExecute()
    {
        $this->helper->method('isEnabled')
            ->willReturn(true);
        $this->helper->method('getDefaultQuery')
            ->willReturn('*');
        $request = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->method('getParam')
            ->withAnyParameters()
            ->willReturn('param');
        $url = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $url->method('getBaseUrl')
            ->willReturn('http://example.com');
        $this->response->method('sendResponse')
            ->willReturn(null);
        $this->context->method('getRequest')
            ->willReturn($request);
        $this->context->method('getUrl')
            ->willReturn($url);
        $this->context->method('getResponse')
            ->willReturn($this->response);

        $catalogSearchResult = $this->getMockBuilder(CatalogSearchResult::class)
            ->setMethods(array('_redirect'))
            ->setConstructorArgs(array($this->context, $this->session, $this->storeManagerInterface, $this->queryFactory, $this->resolver, $this->helper))
            ->getMock();
        $catalogSearchResult->method('_redirect')
            ->willReturn(null);

        $this->assertNull($catalogSearchResult->execute());
    }

    public function testExecuteWhenNotEnabled()
    {
        $storeInterface = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $storeInterface->method('getId')
            ->willReturn(12345);
        $this->response->method('setRedirect')
            ->willReturn($this->response);
        $redirectInterface = $this->getMockBuilder(RedirectInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $redirectInterface->method('getRedirectUrl')
            ->willReturn('http://example.com');
        $query = $this->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->getMock();
        $query->method('setStoreId')
            ->withAnyParameters()
            ->willReturn(null);
        $query->method('getQueryText')
            ->willReturn('');
        $this->queryFactory->method('get')
            ->willReturn($query);
        $this->resolver->method('create')
            ->willReturn(null);
        $this->storeManagerInterface->method('getStore')
            ->willReturn($storeInterface);
        $this->context->method('getResponse')
            ->willReturn($this->response);
        $this->context->method('getRedirect')
            ->willReturn($redirectInterface);

        $catalogSearchResult = new CatalogSearchResult($this->context, $this->session, $this->storeManagerInterface, $this->queryFactory, $this->resolver, $this->helper);

        $this->assertNull($catalogSearchResult->execute());
    }
}
