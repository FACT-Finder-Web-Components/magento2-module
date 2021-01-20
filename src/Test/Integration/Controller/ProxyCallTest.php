<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Integration\Controller;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\TestFramework\TestCase\AbstractController;
use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Client\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @testdox Omikron\Factfinder\Controller\Proxy\Call
 */
class ProxyCallTest extends AbstractController
{
    /** @var MockObject|ClientBuilder */
    private $clientBuilderMock;

    /** @var MockObject|ClientInterface */
    private $clientMock;

    public function test_JSON_endpoints_are_accepted_by_the_proxy_controller()
    {
        $this->clientMock->expects($this->atLeastOnce())
            ->method('request')
            ->with('GET', $this->stringContains('/Suggest.ff'), $this->anything());

        $this->dispatch('/FACT-Finder/Suggest.ff?query=asd');
        $this->assertSame($this->getResponse()->getStatusCode(), 200);
    }

    public function test_rest_calls_are_accepted_by_the_proxy_controller()
    {
        $this->clientMock->expects($this->atLeastOnce())
            ->method('request')
            ->with($this->anything(), $this->stringContains('/rest/v3/records/'), $this->anything());

        $this->dispatch('/FACT-Finder/rest/v3/records/my_channel?sid=abc');
        $this->assertSame($this->getResponse()->getStatusCode(), 200);
    }

    public function test_other_request_paths_are_ignored()
    {
        $this->clientMock->expects($this->never())->method('request');
        $this->dispatch('/FACT-Finder/non-existing-endpoint');
        $this->assert404NotFound();
    }

    public function test_filter_parameters_are_correctly_encoded()
    {
        $this->clientMock->expects($this->atLeastOnce())
            ->method('request')
            ->with('GET', $this->stringContains('filterCategoryPathROOT=First+Category&filterCategoryPathROOT%2FFirst+Category=Second+Category'));

        $this->dispatch('/FACT-Finder/Search.ff?filterCategoryPathROOT=First+Category&filterCategoryPathROOT%2FFirst+Category=Second+Category');
    }

    public function test_filter_parameters_are_correctly_encoded_2()
    {
        $this->clientMock->expects($this->atLeastOnce())
            ->method('request')
            ->with('GET', $this->stringContains('query=pro&filterabgerundete+Ecken+vorhanden=Ja'));

        $this->dispatch('/FACT-Finder/Search.ff?query=pro&filterabgerundete+Ecken+vorhanden=Ja');
    }

    public function test_post_request_are_send_correctly()
    {
        $this->_request = $this->_objectManager->get(RequestInterface::class);
        $this->_request->setMethod('POST');
        $this->_request->setContent('{"masterId":"123","price":"29.99"}');
        $this->clientMock->expects($this->atLeastOnce())
            ->method('request')
            ->with(
                'POST',
                $this->stringContains('/rest/v3/tracking/'),
                ['body' =>'{"masterId":"123","price":"29.99"}', 'headers' => ['Content-Type' => 'application/json']]
            );
        $this->dispatch('/FACT-Finder/rest/v3/tracking/cart');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientMock = $this->createConfiguredMock(ClientInterface::class,
            ['request' => $this->createConfiguredMock(ResponseInterface::class,
                ['getBody' => $this->createConfiguredMock(StreamInterface::class, ['getContents' => '{"status":"OK"}'])
        ])]);
        $this->clientBuilderMock = $this->createMock(ClientBuilder::class);
        $this->clientBuilderMock->method('withVersion')->willReturn($this->clientBuilderMock);
        $this->clientBuilderMock->method('withServerUrl')->willReturn($this->clientBuilderMock);
        $this->clientBuilderMock->method('withCredentials')->willReturn($this->clientBuilderMock);
        $this->clientBuilderMock->method('build')->willReturn($this->clientMock);
        $this->_objectManager->addSharedInstance($this->clientBuilderMock, ClientBuilder::class);
    }

    protected function tearDown(): void
    {
        $this->_objectManager->removeSharedInstance(ClientBuilder::class);
        parent::tearDown();
    }
}
