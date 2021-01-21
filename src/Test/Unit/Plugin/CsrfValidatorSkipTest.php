<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Plugin;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\CsrfValidator;
use Magento\Framework\App\RequestInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CsrfValidatorSkipTest extends TestCase
{
    /** @var MockObject|CsrfValidator */
    private $validatorMock;

    /** @var MockObject|RequestInterface */
    private $requestMock;

    /** @var MockObject|ActionInterface */
    private $actionMock;

    /** @var CsrfValidatorSkip */
    private $plugin;

    public function test_factfinder_frontname_will_be_passed()
    {
        $cb = $this->createPartialMock(\stdClass::class, ['__invoke']);
        $cb->expects($this->never())->method('__invoke');
        $this->requestMock->method('getModuleName')->willReturn('factfinder');
        $this->plugin->aroundValidate($this->validatorMock, $cb, $this->requestMock, $this->createMock(ActionInterface::class));
    }

    public function test_other_frontnames_will_be_validated()
    {
        $cb = $this->createPartialMock(\stdClass::class, ['__invoke']);
        $cb->expects($this->once())->method('__invoke');
        $this->requestMock->method('getModuleName')->willReturn('customer');
        $this->plugin->aroundValidate($this->validatorMock, $cb, $this->requestMock, $this->createMock(ActionInterface::class));
    }

    public function setUp(): void
    {
        $this->requestMock = $this->createMock(RequestInterface::class);
        $this->actionMock  = $this->createMock(ActionInterface::class);
        $this->plugin      = new CsrfValidatorSkip();
    }
}
