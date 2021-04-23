<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Framework\App\ActionFlag;
use Magento\Framework\Event\Observer;
use Magento\Framework\HTTP\Authentication;
use Omikron\Factfinder\Model\Export\BasicAuth;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers ExportAuthentication
 */
class ExportAuthenticationTest extends TestCase
{
    /** @var ExportAuthentication */
    private $observer;

    /** @var MockObject|ActionFlag */
    private $flagMock;

    /** @var MockObject|Authentication */
    private $authMock;

    public function test_it_checks_user_authentication_before_dispatch()
    {
        $this->authMock->method('authenticate')->willReturn(true);
        $this->flagMock->expects($this->never())->method('set');
        $this->observer->execute(new Observer());
    }

    public function test_it_prevents_dispatch_if_the_user_is_not_authorized()
    {
        $this->authMock->method('authenticate')->willReturn(false);
        $this->flagMock->expects($this->once())->method('set')->with('', 'no-dispatch', true);
        $this->observer->execute(new Observer());
    }

    protected function setUp(): void
    {
        $this->authMock = $this->createMock(BasicAuth::class);
        $this->flagMock = $this->createMock(ActionFlag::class);
        $credentials    = $this->createMock(Authentication::class);
        $credentials->method('getCredentials')->willReturn(['Aladdin', 'OpenSesame']);
        $this->observer = new ExportAuthentication($this->flagMock, $this->authMock, $credentials);
    }
}
