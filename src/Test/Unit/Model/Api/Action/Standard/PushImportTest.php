<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api\Action\Standard;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\ClientInterfaceFactory;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Model\Api\Credentials;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PushImportTest extends TestCase
{
    /** @var MockObject|ClientInterfaceFactory */
    private $clientFactoryMock;

    /** @var MockObject|ClientInterface */
    private $factFinderClientMock;

    /** @var MockObject|CommunicationConfigInterface */
    private $communicationConfigMock;

    /** @var MockObject|ScopeConfigInterface */
    private $scopeConfigMock;

    /** @var PushImport */
    private $pushImport;

    public function test_execute_should_not_trigger_import_if_PushImport_is_disabled()
    {
        $this->communicationConfigMock->method('isPushImportEnabled')->willReturn(false);
        $this->factFinderClientMock->expects($this->never())->method('get');
        $this->assertFalse($this->pushImport->execute(1));
    }

    public function test_execute_should_not_trigger_import_if_no_data_type_is_configured()
    {
        $this->scopeConfigMock->method('getValue')->with('factfinder/data_transfer/ff_push_import_type', 'store', 1)->willReturn('');
        $this->factFinderClientMock->expects($this->never())->method('get');
        $this->assertFalse($this->pushImport->execute(1));
    }

    public function test_execute_should_return_true_if_no_error()
    {
        $this->communicationConfigMock->method('isPushImportEnabled')->willReturn(true);
        $this->scopeConfigMock->method('getValue')->with('factfinder/data_transfer/ff_push_import_type', 'store', 1)->willReturn('data,suggest');
        $this->factFinderClientMock->expects($this->exactly(2))->method('get')->willReturn(['success' => true]);
        $this->assertTrue($this->pushImport->execute(1));
    }

    /**
     * @testWith ["errors"]
     *           ["error"]
     */
    public function test_execute_should_return_false_if_response_contains_errors(string $param)
    {
        $this->communicationConfigMock->method('isPushImportEnabled')->willReturn(true);
        $this->scopeConfigMock->method('getValue')->with('factfinder/data_transfer/ff_push_import_type', 'store', 1)->willReturn('data,suggest');
        $this->factFinderClientMock->expects($this->exactly(2))->method('get')->willReturn([$param => 'There were an error during push import']);
        $this->assertFalse($this->pushImport->execute(1));
    }

    protected function setUp(): void
    {
        $this->factFinderClientMock = $this->createMock(ClientInterface::class);

        $this->clientFactoryMock = $this->getMockBuilder(ClientInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->clientFactoryMock->method('create')->willReturn($this->factFinderClientMock);

        $this->communicationConfigMock = $this->createMock(CommunicationConfigInterface::class);
        $this->scopeConfigMock         = $this->createMock(ScopeConfigInterface::class);
        $this->scopeConfigMock         = $this->createMock(ScopeConfigInterface::class);
        $this->communicationConfigMock->method('getChannel')->willReturn('test-channel');
        $this->communicationConfigMock->method('getAddress')->willReturn('http://fake-factfinder.com/FACT-Finder-7.3');

        $this->pushImport = new PushImport(
            $this->clientFactoryMock,
            $this->communicationConfigMock,
            $this->scopeConfigMock,
            $this->createMock(Credentials::class)
        );
    }
}
