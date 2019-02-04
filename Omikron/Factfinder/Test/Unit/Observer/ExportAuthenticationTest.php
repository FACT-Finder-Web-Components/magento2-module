<?php

namespace Omikron\Factfinder\Observer;

use Magento\Framework\App\ActionFlag;
use Magento\Framework\Event\Observer;
use Magento\Framework\HTTP\Authentication;
use Omikron\Factfinder\Model\Export\BasicAuth;
use PHPUnit\Framework\TestCase;

class ExportAuthenticationTest extends TestCase
{
    /** @var ExportAuthentication */
    private $observer;

    /** @var ActionFlag */
    private $flagMock;

    /** @var Authentication */
    private $authMock;

    public function testPreventDispatch()
    {
        $this->authMock->method('authenticate')->willReturn(false);
        $this->flagMock->expects($this->once())->method('set')->with('', 'no-dispatch', true);
        $this->observer->execute(new Observer());
    }

    public function testAuthenticateUserAndDispatch()
    {
        $this->authMock->method('authenticate')->willReturn(true);
        $this->flagMock->expects($this->never())->method('set');
        $this->observer->execute(new Observer());
    }

    protected function setUp()
    {
        $this->authMock = $this->createMock(BasicAuth::class);
        $this->flagMock = $this->createMock(ActionFlag::class);
        $credentials = $this->createMock(Authentication::class);
        $credentials->method('getCredentials')->willReturn(['Aladdin', 'OpenSesame']);
        $this->observer = new ExportAuthentication($this->flagMock, $this->authMock, $credentials);
    }
}
