<?php

namespace Omikron\Factfinder\Test\Unit\Helper;

use Omikron\Factfinder\Helper\ResultRefiner;
use \Magento\Framework\Module\Manager;
use \Psr\Log\LoggerInterface;
use \Magento\Framework\App\RequestInterface;
use \Magento\Framework\UrlInterface;
use \Magento\Framework\HTTP\Header;
use \Magento\Framework\Event\ManagerInterface;
use \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use \Magento\Framework\Cache\ConfigInterface;
use \Magento\Framework\Url\EncoderInterface;
use \Magento\Framework\Url\DecoderInterface;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\App\Helper\Context;

class ResultRefinerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Omikron\Factfinder\Helper\ResultRefiner
     */
    protected $resultRefiner;

    public function setUp()
    {
        $manager = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $loggerInterface = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $requestInterface = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $urlInterface = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $header = $this->getMockBuilder(Header::class)
            ->disableOriginalConstructor()
            ->getMock();
        $managerInterface = $this->getMockBuilder(ManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $remoteAddress = $this->getMockBuilder(RemoteAddress::class)
            ->disableOriginalConstructor()
            ->getMock();
        $configInterface = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $encoderInterface = $this->getMockBuilder(EncoderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $decoderInterface = $this->getMockBuilder(DecoderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $scopeConfigInterface = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $context->method('getModuleManager')
            ->willReturn($manager);
        $context->method('getLogger')
            ->willReturn($loggerInterface);
        $context->method('getRequest')
            ->willReturn($requestInterface);
        $context->method('getUrlBuilder')
            ->willReturn($urlInterface);
        $context->method('getHttpHeader')
            ->willReturn($header);
        $context->method('getEventManager')
            ->willReturn($managerInterface);
        $context->method('getRemoteAddress')
            ->willReturn($remoteAddress);
        $context->method('getCacheConfig')
            ->willReturn($configInterface);
        $context->method('getUrlDecoder')
            ->willReturn($encoderInterface);
        $context->method('getUrlEncoder')
            ->willReturn($decoderInterface);
        $context->method('getScopeConfig')
            ->willReturn($scopeConfigInterface);

        $this->resultRefiner = new ResultRefiner($context);
    }

    public function testRefine()
    {
        $this->assertEquals('test', $this->resultRefiner->refine('test'));
    }
}
