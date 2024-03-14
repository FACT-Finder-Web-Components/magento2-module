<?php

namespace Omikron\Factfinder\Test\Unit\Service;

use InvalidArgumentException;
use Magento\Framework\Filesystem;
use Omikron\Factfinder\Service\FeedFileService;
use PHPUnit\Framework\TestCase;

/**
 * @covers FeedFileService
 */
class FeedFileServiceTest extends TestCase
{
    private FeedFileService $feedFileService;

    /**
     * @dataProvider validDataProvider
     * @throws \Exception
     */
    public function test_will_return_filename(string $exportType, string $channel, string $expected)
    {
        $result = $this->feedFileService->getFeedExportFilename($exportType, $channel);
        $this->assertSame($expected, $result);
    }

    public function test_will_throw_exception_on_empty_export_type()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Export type must not be empty');
        $this->assertSame('export.test_type.test_channel.csv', $this->feedFileService->getFeedExportFilename('', 'test_channel'));
    }

    public function test_will_throw_exception_on_empty_export_channel()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Channel must not be empty');
        $this->assertSame('export.test_type.test_channel.csv', $this->feedFileService->getFeedExportFilename('test_type', ''));
    }

    public static function validDataProvider(): array
    {
        return [
            [
                'test_type', 'test_channel', 'export.test_type.test_channel.csv'
            ]
        ];
    }

    protected function setUp(): void
    {
        $this->feedFileService = new FeedFileService($this->createMock(Filesystem::class));
    }
}
