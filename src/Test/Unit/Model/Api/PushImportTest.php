<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\FactFinder\Communication\Resource\Builder;
use Omikron\FactFinder\Communication\ResourceInterface;
use Omikron\Factfinder\Model\Config\ExportConfig;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class PushImportTest extends TestCase
{
    /** @var MockObject|ClientInterface */
    private $factFinderClientMock;

    /** @var MockObject|CommunicationConfigInterface */
    private $communicationConfigMock;

    /** @var MockObject|ScopeConfigInterface */
    private $scopeConfigMock;

    /** @var MockObject|CredentialsFactory */
    private $credentialsFactoryMock;

    /** @var MockObject|ExportConfig */
    private $exportConfigMock;

    /** @var MockObject|Builder */
    private $builderMock;

    /** @var MockObject|ResourceInterface */
    private $resourceMock;

    /** @var PushImport */
    private $pushImport;

    public function test_execute_should_not_trigger_import_if_no_data_type_is_configured()
    {
        $this->scopeConfigMock->method('getValue')->with('factfinder/data_transfer/ff_push_import_type', 'store', 1)->willReturn([]);
        $this->resourceMock->expects($this->never())->method('import');
        $this->assertFalse($this->pushImport->execute(1));
    }


    public function test_execute_should_return_true_if_no_error()
    {
        $this->exportConfigMock->method('getPushImportDataTypes')->willReturn(['data, suggest']);
        $this->builderMock->expects($this->once())->method('withApiVersion')->with('7.3');
        $this->builderMock->expects($this->once())->method('withServerUrl')->with('http://fake-factfinder.com/FACT-Finder-7.3');
        $this->resourceMock->method('import')->willReturn(['success' => true]);
        $this->assertTrue($this->pushImport->execute(1));
    }

    /**
     * @testWith ["errors"]
     *           ["error"]
     */
    public function test_execute_should_return_false_if_response_contains_errors(string $param)
    {
        $this->exportConfigMock->method('getPushImportDataTypes')->willReturn(['data, suggest']);
        $this->resourceMock->method('import')->willReturn([$param => 'There were an error during push import']);
        $this->assertFalse($this->pushImport->execute(1));
    }

    protected function setUp(): void
    {
        $this->communicationConfigMock = $this->createMock(CommunicationConfigInterface::class);
        $this->scopeConfigMock         = $this->createMock(ScopeConfigInterface::class);
        $this->communicationConfigMock->method('getAddress')->willReturn('http://fake-factfinder.com/FACT-Finder-7.3');
        $this->communicationConfigMock->method('getVersion')->willReturn('7.3');
        $this->exportConfigMock       = $this->createMock(ExportConfig::class);
        $this->credentialsFactoryMock = $this->createConfiguredMock(CredentialsFactory::class, ['create' => $this->createMock(\Omikron\FactFinder\Communication\Credentials::class)]);
        $this->resourceMock           = $this->createMock(ResourceInterface::class);
        $this->builderMock            = $this->createMock(Builder::class);
        $this->builderMock->method('withApiVersion')->willReturn($this->builderMock);
        $this->builderMock->method('withServerUrl')->willReturn($this->builderMock);
        $this->builderMock->method('withCredentials')->willReturn($this->builderMock);
        $this->builderMock->method('withLogger')->willReturn($this->builderMock);
        $this->builderMock->method('build')->willReturn($this->resourceMock);

        $this->pushImport = new PushImport(
            $this->builderMock,
            $this->credentialsFactoryMock,
            $this->communicationConfigMock,
            $this->exportConfigMock,
            $this->createMock(LoggerInterface::class)
        );
    }
}
