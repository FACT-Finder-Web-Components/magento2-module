<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api;

use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Client\ClientInterface;
use Omikron\FactFinder\Communication\Credentials;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\Config\ExportConfig;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * @covers PushImport
 */
class PushImportTest extends TestCase
{
    /** @var MockObject|ClientInterface */
    private $factFinderClientMock;

    /** @var MockObject|CommunicationConfig */
    private $communicationConfigMock;

    /** @var MockObject|ExportConfig */
    private $exportConfigMock;

    /** @var MockObject|ClientBuilder */
    private $builderMock;

    /** @var MockObject|ClientInterface */
    private $clientMock;

    /** @var PushImport */
    private $pushImport;

    public function test_should_throw_if_import_is_running()
    {
        $this->exportConfigMock->method('getPushImportDataTypes')->with($this->anything())->willReturn(['search','suggest']);
        $this->clientMock->method('request')->with('GET', 'rest/v4/import/running', $this->anything())
            ->willReturn($this->importRunningResponse());
        $this->expectExceptionMessage("Can't start a new import process. Another one is still going");
        $this->pushImport->execute(1);
    }

    public function test_execute_should_not_trigger_import_if_no_data_type_is_configured()
    {
        $this->exportConfigMock->method('getPushImportDataTypes')->with($this->anything())->willReturn([]);
        $this->clientMock->expects($this->never())->method('request');

        $this->assertFalse($this->pushImport->execute(1));
    }

    public function test_execute_should_return_true_if_no_error()
    {
        $this->exportConfigMock->method('getPushImportDataTypes')->willReturn(['data', 'suggest']);

        $this->clientMock->expects($this->exactly(3))
            ->method('request')
            ->withConsecutive(
                ['GET', $this->stringContains('running'), $this->anything()],
                ['POST', $this->stringContains('import/data'), $this->anything()],
                ['POST', $this->stringContains('import/suggest'), $this->anything()]
            )->willReturnOnConsecutiveCalls(
                $this->importNotRunningResponse(),
                $this->importResponseOk(),
                $this->importResponseOk()
            );
        $this->assertTrue($this->pushImport->execute(1));
    }

    protected function setUp(): void
    {
        $this->communicationConfigMock = $this->createMock(CommunicationConfig::class);
        $this->communicationConfigMock->method('getAddress')->willReturn('http://fake-factfinder.com/FACT-Finder-7.3');
        $this->communicationConfigMock->method('getVersion')->willReturn('ng');

        $this->exportConfigMock = $this->createMock(ExportConfig::class);
        $this->clientMock       = $this->createMock(ClientInterface::class);

        $this->builderMock = $this->createMock(ClientBuilder::class);
        $this->builderMock->method('withVersion')->willReturn($this->builderMock);
        $this->builderMock->method('withServerUrl')->willReturn($this->builderMock);
        $this->builderMock->method('withCredentials')->willReturn($this->builderMock);
        $this->builderMock->method('build')->willReturn($this->clientMock);

        $this->pushImport = new PushImport(
            $this->builderMock,
            $this->createConfiguredMock(CredentialsFactory::class, ['create' => $this->createMock(Credentials::class)]),
            $this->communicationConfigMock,
            $this->exportConfigMock,
            $this->createMock(LoggerInterface::class)
        );
    }

    private function importRunningResponse(): ResponseInterface
    {
        return $this->createConfiguredMock(ResponseInterface::class, ['getBody' => 'true']);
    }

    private function importNotRunningResponse(): ResponseInterface
    {
        return $this->createConfiguredMock(ResponseInterface::class, ['getBody' => 'false']);
    }

    private function importResponseOk(): ResponseInterface
    {
        return $this->createConfiguredMock(ResponseInterface::class, ['getBody' => '{"0": {"importType": "data", "statusMessages": {},"errorMessages": {}}}']);
    }

    private function importResponseBad(string $errorField): ResponseInterface
    {
        return $this->createConfiguredMock(ResponseInterface::class, [
            'getBody' => json_encode([$errorField => 'There were an error during push import']),
        ]);
    }
}
