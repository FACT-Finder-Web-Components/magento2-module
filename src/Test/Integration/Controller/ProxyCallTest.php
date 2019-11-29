<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Integration\Controller;

use Magento\TestFramework\TestCase\AbstractController;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Model\Client;
use PHPUnit\Framework\MockObject\MockObject;

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

    public function test_Rest_calls_are_accepted_by_the_proxy_controller()
    {
        $this->apiClient->expects($this->atLeastOnce())
            ->method('sendRequest')
            ->with($this->stringContains('/rest/v1/records/'), $this->anything());

        $this->dispatch('/FACT-Finder/rest/v1/records/my_channel?sid=abc');
        $this->assertSame($this->getResponse()->getStatusCode(), 200);
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
