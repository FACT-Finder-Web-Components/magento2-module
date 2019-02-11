<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Model\Consumer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Model\Consumer\TestConnection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class TestConnectionTest extends TestCase
{
    /** @var MockObject|ClientInterface */
    private $factFinderClientMock;

    /** @var MockObject|CommunicationConfigInterface */
    private $communicationConfigMock;

    /** @var SerializerInterface|ScopeConfigInterface */
    private $serializerMock;

    /** @var TestConnection */
    private $testConnection;

    public function test_execute_should_return_true_if_no_exceptions_is_thrown()
    {
        $this->factFinderClientMock->expects($this->once())->method('sendRequest')->willReturn(['success' => true]);

        $this->assertTrue($this->testConnection->execute(1));
    }

    protected function setUp()
    {
        $this->factFinderClientMock    = $this->createMock(ClientInterface::class);
        $this->communicationConfigMock = $this->createMock(CommunicationConfigInterface::class);
        $this->serializerMock          = $this->createMock(SerializerInterface::class);
        $this->communicationConfigMock->method('getChannel')->willReturn('test-channel');
        $this->communicationConfigMock->method('getAddress')->willReturn('http://fake-factfinder.com/FACT-Finder-7.3');

        $this->testConnection = new TestConnection(
            $this->factFinderClientMock,
            $this->communicationConfigMock,
            $this->serializerMock
        );
    }
}
