<?php

namespace Omikron\Factfinder\Test\Unit\Controller\Proxy;

use Omikron\Factfinder\Controller\Proxy\Call;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\Result\JsonFactory;
use \Omikron\Factfinder\Helper\ResultRefiner;
use \Omikron\Factfinder\Helper\Communication;
use \Magento\Framework\Controller\Result\Json;
use \Magento\Framework\App\Request\Http;

class CallTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Omikron\Factfinder\Controller\Proxy\Call
     */
    protected $call;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    public function setUp()
    {
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $json = $this->getMockBuilder(Json::class)
            ->disableOriginalConstructor()
            ->getMock();
        $json->method('setJsonData')
            ->willReturn($json);
        $jsonResultFactory = $this->getMockBuilder(JsonFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $jsonResultFactory->method('create')
            ->willReturn($json);
        $resultRefiner = $this->getMockBuilder(ResultRefiner::class)
            ->disableOriginalConstructor()
            ->getMock();
        $communication = $this->getMockBuilder(Communication::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = $this->getMockBuilder(Http::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->call = $this->getMockBuilder(Call::class)
            ->setMethods(array('getRequest'))
            ->setConstructorArgs(array($context, $jsonResultFactory, $resultRefiner, $communication))
            ->getMock();
    }

    public function testExecuteWhenWrongPathInfo()
    {
        $this->request->method('getPathInfo')
            ->willReturn('FACT-Finder/apiname.ff?key=value');
        $this->call->method('getRequest')
            ->willReturn($this->request);

        $this->assertInstanceOf(Json::class, $this->call->execute());
    }

    public function testExecute()
    {
        $this->request->method('getPathInfo')
            ->willReturn('FACT-Finder/Apiname.ff');
        $this->call->method('getRequest')
            ->willReturn($this->request);

        $this->assertInstanceOf(Json::class, $this->call->execute());
    }

}
