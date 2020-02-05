<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Integration\Controller;

use Magento\TestFramework\TestCase\AbstractController;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Model\Client;
use PHPUnit\Framework\Constraint\ArraySubset;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @testdox Omikron\Factfinder\Controller\Proxy\Call
 */
class ProxyCallTest extends AbstractController
{
    /** @var MockObject|ClientInterface */
    private $apiClient;

    public function test_JSON_endpoints_are_accepted_by_the_proxy_controller()
    {
        $this->apiClient->expects($this->atLeastOnce())
            ->method('sendRequest')
            ->with($this->stringContains('/Suggest.ff'), $this->anything());

        $this->dispatch('/FACT-Finder/Suggest.ff?query=asd');
        $this->assertSame($this->getResponse()->getStatusCode(), 200);
    }

    public function test_rest_calls_are_accepted_by_the_proxy_controller()
    {
        $this->apiClient->expects($this->atLeastOnce())
            ->method('sendRequest')
            ->with($this->stringContains('/rest/v1/records/'), $this->anything());

        $this->dispatch('/FACT-Finder/rest/v1/records/my_channel?sid=abc');
        $this->assertSame($this->getResponse()->getStatusCode(), 200);
    }

    public function test_other_request_paths_are_ignored()
    {
        $this->apiClient->expects($this->never())->method('sendRequest');
        $this->dispatch('/FACT-Finder/non-existing-endpoint');
        $this->assert404NotFound();
    }

    public function test_filter_parameters_are_correctly_encoded()
    {
        $this->apiClient->expects($this->atLeastOnce())
            ->method('sendRequest')
            ->with($this->anything(), new ArraySubset(['filterCategoryPathROOT/First Category' => 'Second Category']));

        $this->dispatch('/FACT-Finder/Search.ff?filterCategoryPathROOT=First+Category&filterCategoryPathROOT%2FFirst+Category=Second+Category&a=b');
    }

    protected function setUp()
    {
        parent::setUp();
        $this->apiClient = $this->createMock(ClientInterface::class);
        $this->_objectManager->addSharedInstance($this->apiClient, Client::class);
    }

    protected function tearDown()
    {
        $this->_objectManager->removeSharedInstance(Client::class);
        parent::tearDown();
    }
}
