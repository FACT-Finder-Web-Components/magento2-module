<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\TestConnection;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\Controller\Result\JsonFactory;
use Omikron\Factfinder\Api\Config\AuthConfigInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\FactFinder\Communication\Credentials;
use Omikron\FactFinder\Communication\Resource\Builder;
use Omikron\FactFinder\Communication\ResourceInterface;
use Omikron\Factfinder\Model\Api\CredentialsFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TestConnectionTest extends TestCase
{
    /** @var TestConnection */
    private $controller;

    /** @var MockObject|RequestInterface */
    private $request;

    /** @var MockObject|Builder */
    private $builderMock;

    /** @var MockObject|ResourceInterface */
    private $resourceMock;

    public function test_prevent_errors_without_post_data()
    {
        $this->request->method('getParam')->willReturn('foobar');
        $this->request->method('getParams')->willReturn([]);
        $this->controller->execute();
        $this->assertNull($this->getExpectedException());
    }

    protected function setUp(): void
    {
        $credentialsFactory = $this->createConfiguredMock(CredentialsFactory::class, ['create' => $this->createMock(Credentials::class)]);
        $this->request           = $this->createMock(RequestInterface::class);

        $this->resourceMock = $this->createMock(ResourceInterface::class);
        $this->builderMock  = $this->createMock(Builder::class);
        $this->builderMock->method('withApiVersion')->willReturn($this->builderMock);
        $this->builderMock->method('withServerUrl')->willReturn($this->builderMock);
        $this->builderMock->method('withCredentials')->willReturn($this->builderMock);
        $this->builderMock->method('withLogger')->willReturn($this->builderMock);
        $this->builderMock->method('build')->willReturn($this->resourceMock);

        $this->controller = new TestConnection(
            $this->createConfiguredMock(Context::class, ['getRequest' => $this->request]),
            $this->createConfiguredMock(JsonFactory::class, ['create' => $this->createMock(JsonResult::class)]),
            $credentialsFactory,
            $this->createMock(AuthConfigInterface::class),
            $this->createMock(CommunicationConfigInterface::class),
            $this->builderMock
        );
    }
}
