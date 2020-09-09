<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\TestConnection;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\Controller\Result\JsonFactory;
use Omikron\Factfinder\Model\Config\FtpConfig;
use Omikron\Factfinder\Model\FtpUploader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TestFtpConnectionTest extends TestCase
{
    /** @var TestFtpConnection */
    private $controller;

    /** @var MockObject|RequestInterface */
    private $request;

    /** @var MockObject|FtpUploader */
    private $ftpUploader;

    /** @var MockObject|JsonResult */
    private $jsonResult;

    public function test_prevent_errors_without_post_data()
    {
        $this->request->method('getParam')->willReturn('foobar');
        $this->request->method('getParams')->willReturn([]);
        $this->controller->execute();
        $this->assertNull($this->getExpectedException());
    }

    public function test_execute_should_return_json_response_with_exception_message_if_thrown_in_ftp_client()
    {
        $expectedMessage = 'The username or password is invalid. Verify both and try again.';
        $this->ftpUploader
            ->expects($this->once())
            ->method('testConnection')
            ->with($this->anything())
            ->willThrowException(new \Exception($expectedMessage));

        $this->jsonResult->expects($this->once())
            ->method('setData')
            ->with(['message' => $expectedMessage]);

        $this->controller->execute([]);
    }

    public function test_execute_should_return_success_message_if_no_exception_thrown_in_ftp_client()
    {
        $expected = 'Connection successfully established.';
        $this->ftpUploader
            ->expects($this->once())
            ->method('testConnection')
            ->with($this->anything());

        $this->jsonResult->expects($this->once())
            ->method('setData')
            ->with(['message' => $expected]);

        $this->controller->execute([]);
    }

    protected function setUp(): void
    {
        $this->request      = $this->createConfiguredMock(RequestInterface::class, ['getParams' =>[]]);
        $this->ftpUploader  = $this->createMock(FtpUploader::class);
        $this->jsonResult   = $this->createConfiguredMock(JsonResult::class, ['setData' => $this]);
        $this->controller   = new TestFtpConnection(
            $this->createConfiguredMock(Context::class, ['getRequest' => $this->request]),
            $this->createConfiguredMock(JsonFactory::class, ['create' => $this->jsonResult]),
            $this->ftpUploader,
            $this->createMock(FtpConfig::class)
        );
    }
}
