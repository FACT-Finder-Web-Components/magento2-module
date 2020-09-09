<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api\Action\Standard;

use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\ClientInterfaceFactory;
use Omikron\Factfinder\Model\Api\ClientFactory;
use Omikron\Factfinder\Model\Api\Credentials;
use PHPUnit\Framework\TestCase;

class TestConnectionTest extends TestCase
{
    /** @var TestConnection */
    private $testConnection;

    public function test_execute_should_return_true_if_no_exception_is_thrown()
    {
        $this->assertTrue($this->testConnection->execute('http://fake-ff-server.com', []));
    }

    protected function setUp(): void
    {
        $clientFactoryMock = $this->getMockBuilder(ClientInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $clientFactoryMock->method('create')->willReturn($this->createConfiguredMock(ClientInterface::class, ['get' => []]));
        $this->testConnection = new TestConnection($clientFactoryMock, $this->createMock(Credentials::class));
    }
}
