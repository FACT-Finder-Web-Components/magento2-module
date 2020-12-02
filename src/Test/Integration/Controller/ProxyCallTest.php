<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Integration\Controller;

use Magento\TestFramework\TestCase\AbstractController;
use Omikron\FactFinder\Communication\ClientInterface;
use Omikron\FactFinder\Communication\Resource\Builder;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @testdox Omikron\Factfinder\Controller\Proxy\Call
 */
class ProxyCallTest extends AbstractController
{
    /** @var MockObject|Builder */
    private $builderMock;

    /** @var MockObject|ClientInterface */
    private $clientMock;

    public function test_JSON_endpoints_are_accepted_by_the_proxy_controller()
    {
        $this->clientMock->expects($this->atLeastOnce())
            ->method('getRequest')
            ->with($this->stringContains('/Suggest.ff'), $this->anything());

        $this->dispatch('/FACT-Finder/Suggest.ff?query=asd');
        $this->assertSame($this->getResponse()->getStatusCode(), 200);
    }

    public function test_rest_calls_are_accepted_by_the_proxy_controller()
    {
        $this->clientMock->expects($this->atLeastOnce())
            ->method('getRequest')
            ->with($this->stringContains('/rest/v1/records/'), $this->anything());

        $this->dispatch('/FACT-Finder/rest/v1/records/my_channel?sid=abc');
        $this->assertSame($this->getResponse()->getStatusCode(), 200);
    }

    public function test_other_request_paths_are_ignored()
    {
        $this->clientMock->expects($this->never())->method('getRequest');
        $this->dispatch('/FACT-Finder/non-existing-endpoint');
        $this->assert404NotFound();
    }

    public function test_filter_parameters_are_correctly_encoded()
    {
        $this->clientMock->expects($this->atLeastOnce())
            ->method('getRequest')
            ->with($this->anything(), ['filterCategoryPathROOT' => 'First Category', 'filterCategoryPathROOT/First Category' => 'Second Category']);

        $this->dispatch('/FACT-Finder/Search.ff?filterCategoryPathROOT=First+Category&filterCategoryPathROOT%2FFirst+Category=Second+Category');
    }

    public function test_filter_parameters_are_correctly_encoded_2()
    {
        $this->clientMock->expects($this->atLeastOnce())
            ->method('getRequest')
            ->with($this->anything(), ['filterabgerundete Ecken vorhanden' => 'Ja', 'query' => 'pro']);

        $this->dispatch('/FACT-Finder/Search.ff?query=pro&filterabgerundete+Ecken+vorhanden=Ja');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientMock  = $this->createConfiguredMock(ClientInterface::class, ['getRequest' => []]);
        $this->builderMock = $this->createMock(Builder::class);
        $this->builderMock->method('withApiVersion')->willReturn($this->builderMock);
        $this->builderMock->method('withServerUrl')->willReturn($this->builderMock);
        $this->builderMock->method('withCredentials')->willReturn($this->builderMock);
        $this->builderMock->method('withLogger')->willReturn($this->builderMock);
        $this->builderMock->method('client')->willReturn($this->clientMock);

        $this->_objectManager->addSharedInstance($this->builderMock, Builder::class);
    }

    protected function tearDown(): void
    {
        $this->_objectManager->removeSharedInstance(Builder::class);
        parent::tearDown();
    }
}
