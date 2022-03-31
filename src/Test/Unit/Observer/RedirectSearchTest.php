<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers RedirectSearch
 */
class RedirectSearchTest extends TestCase
{
    private RedirectSearch $observer;

    /** @var MockObject|CommunicationConfig */
    private MockObject $config;

    /** @var MockObject|RedirectInterface */
    private MockObject $redirect;

    /** @var MockObject|ResponseInterface */
    private MockObject $response;

    public function test_it_is_an_observer()
    {
        $this->assertInstanceOf(ObserverInterface::class, $this->observer);
    }

    /**
     * @testdox No redirect takes place if FACT-Finder is disabled
     */
    public function testNoRedirectIfDisabled()
    {
        $this->config->method('isChannelEnabled')->willReturn(false);
        $this->redirect->expects($this->never())->method('redirect');
        $this->observer->execute(new Observer());
    }

    /**
     * @testdox Redirect to FACT-Finder search result if enabled
     */
    public function testRedirectIfEnabled()
    {
        $this->config->method('isChannelEnabled')->willReturn(true);

        $request = $this->createMock(RequestInterface::class);
        $request->method('getParam')->with('q', '*')->willReturn('Foobar');

        // Test search query forwarding
        $this->redirect->expects($this->once())
            ->method('redirect')
            ->with($this->response, 'factfinder/result', ['_query' => ['query' => 'Foobar']]);

        // Run!
        $this->observer->execute(new Observer(['request' => $request]));
    }

    protected function setUp(): void
    {
        $this->config   = $this->createMock(CommunicationConfig::class);
        $this->redirect = $this->createMock(RedirectInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
        $this->observer = new RedirectSearch($this->redirect, $this->response, $this->config);
    }
}
