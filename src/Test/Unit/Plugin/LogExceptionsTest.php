<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Omikron\FactFinder\Communication\Client\ClientException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers LogExceptions
 */
class LogExceptionsTest extends TestCase
{
    /** @var LogExceptions */
    private $plugin;

    /** @var MockObject|LoggerInterface */
    private $logger;

    /** @var MockObject|ScopeConfigInterface */
    private $scopeConfig;

    public function test_proceed_if_no_exception_is_raised()
    {
        $this->assertSame(true, $this->plugin->aroundExecute(null, function () {
            return true;
        }));
    }

    public function test_return_false_on_exception()
    {
        $this->assertSame(false, $this->plugin->aroundExecute(null, function () {
            throw new ClientException();
        }));
    }

    public function test_log_exception_if_logging_is_enabled()
    {
        $this->scopeConfig
            ->method('isSetFlag')
            ->with('factfinder/general/logging_enabled', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null)
            ->willReturn(true);

        $this->logger->expects($this->once())->method('error');

        $this->assertSame(false, $this->plugin->aroundExecute(null, function () {
            throw new ClientException();
        }));
    }

    protected function setUp(): void
    {
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $this->logger      = $this->createMock(LoggerInterface::class);
        $this->plugin      = new LogExceptions($this->scopeConfig, $this->logger);
    }
}
