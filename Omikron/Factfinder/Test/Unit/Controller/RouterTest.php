<?php

namespace Omikron\Factfinder\Test\Unit\Controller;

use Omikron\Factfinder\Controller\Router;
use \Magento\Framework\App\ActionFactory;
use \Magento\Framework\App\ResponseInterface;
use \Magento\Framework\App\Request\Http;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Omikron\Factfinder\Controller\Router
     */
    protected $router;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    public function setUp()
    {
        $actionFactory = $this->getMockBuilder(ActionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $response = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = $this->getMockBuilder(Http::class)
            ->setMethods(array('getPathInfo'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->router = new Router($actionFactory, $response);
    }

    public function testMatch()
    {
        $this->request->method('getPathInfo')
            ->willReturn('/FACT-Finder/result');

        $this->router->match($this->request);

        $this->assertEquals('factfinder', $this->request->getModuleName());
        $this->assertEquals('result', $this->request->getControllerName());
        $this->assertEquals('index', $this->request->getActionName());
    }

    public function testMatchProxy()
    {
        $this->request->method('getPathInfo')
            ->willReturn('/FACT-Finder/test');

        $this->router->match($this->request);

        $this->assertEquals('factfinder', $this->request->getModuleName());
        $this->assertEquals('proxy', $this->request->getControllerName());
        $this->assertEquals('call', $this->request->getActionName());
    }

    public function testMatchWhenWrongPathInfo()
    {
        $this->request->method('getPathInfo')
            ->willReturn('/incorrect/path');

        $this->assertFalse($this->router->match($this->request));
    }
}
