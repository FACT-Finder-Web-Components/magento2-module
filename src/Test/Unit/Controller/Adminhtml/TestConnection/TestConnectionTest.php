<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\TestConnection;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\Controller\Result\JsonFactory;
use Omikron\Factfinder\Api\Config\AuthConfigInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Model\Api\Credentials;
use Omikron\Factfinder\Model\Api\CredentialsFactory;
use Omikron\Factfinder\Model\Api\Action\Standard\TestConnection as ApiConnectionTest;
use Omikron\Factfinder\Model\Api\ActionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TestConnectionTest extends TestCase
{
    /** @var TestConnection */
    private $controller;

    /** @var MockObject|ActionFactory */
    private $actionFactoryMock;

    /** @var MockObject|RequestInterface */
    private $request;

    public function test_prevent_errors_without_post_data()
    {
        $this->request->method('getParam')->willReturn('foobar');
        $this->request->method('getParams')->willReturn([]);
        $this->controller->execute();
        $this->assertNull($this->getExpectedException());
    }

    protected function setUp(): void
    {
        $credentialsFactory = $this->getMockBuilder(CredentialsFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $credentialsFactory->method('create')->willReturn($this->createMock(Credentials::class));

        $this->request = $this->createMock(RequestInterface::class);
        $this->actionFactoryMock = $this->createConfiguredMock(ActionFactory::class, ['getTestConnection' => $this->createMock(ApiConnectionTest::class)]);
        $this->actionFactoryMock->method('withCredentials')->with($this->anything())->willReturn($this->actionFactoryMock);
        $this->actionFactoryMock->method('withApiVersion')->with($this->anything())->willReturn($this->actionFactoryMock);

        $this->controller = new TestConnection(
            $this->createConfiguredMock(Context::class, ['getRequest' => $this->request]),
            $this->createConfiguredMock(JsonFactory::class, ['create' => $this->createMock(JsonResult::class)]),
            $credentialsFactory,
            $this->createMock(AuthConfigInterface::class),
            $this->createMock(CommunicationConfigInterface::class),
            $this->actionFactoryMock
        );
    }
}
