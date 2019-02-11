<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Consumer;

use Omikron\Factfinder\Api\ClientInterface;
use PHPUnit\Framework\TestCase;

class TestConnectionTest extends TestCase
{
    /** @var TestConnection */
    private $testConnection;

    public function test_execute_should_return_true_if_no_exception_is_thrown()
    {
        $this->assertTrue($this->testConnection->execute('http://fake-ff-server.com', []));
    }

    protected function setUp()
    {
        $clientMock = $this->createConfiguredMock(ClientInterface::class, ['sendRequest' => []]);
        $this->testConnection = new TestConnection($clientMock);
    }
}
