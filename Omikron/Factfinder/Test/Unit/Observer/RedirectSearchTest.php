<?php

namespace Omikron\Factfinder\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Omikron\Factfinder\Helper\Data as Config;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RedirectSearchTest extends TestCase
{
    /** @var RedirectSearch */
    private $observer;

    /** @var Config|MockObject */
    private $config;

    /** @var RedirectInterface|MockObject */
    private $redirect;

    /** @var ResponseInterface|MockObject */
    private $response;

    public function testIsObserver()
    {
        $this->assertInstanceOf(ObserverInterface::class, $this->observer);
    }

    public function testNoRedirectIfDisabled()
    {
        $this->config->method('isEnabled')->willReturn(false);
        $this->redirect->expects($this->never())->method('redirect');
        $this->observer->execute(new Observer());
    }

    public function testRedirectIfEnabled()
    {
        $this->config->method('isEnabled')->willReturn(true);
        $this->config->method('getDefaultQuery')->willReturn('*');

        $request = $this->createMock(RequestInterface::class);
        $request->method('getParam')->with('q', '*')->willReturn('Foobar');

        // Test search query forwarding
        $query = ['_query' => ['query' => 'Foobar']];
        $this->redirect->expects($this->once())->method('redirect')->with($this->response, 'FACT-Finder/result', $query);

        // Run!
        $this->observer->execute(new Observer(['request' => $request]));
    }

    protected function setUp()
    {
        $this->config   = $this->createMock(Config::class);
        $this->redirect = $this->createMock(RedirectInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
        $this->observer = new RedirectSearch($this->redirect, $this->response, $this->config);
    }
}
