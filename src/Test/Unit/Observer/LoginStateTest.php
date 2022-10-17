<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Session\Config\ConfigInterface as SessionConfig;
use Omikron\Factfinder\Model\SessionData;
use Omikron\Factfinder\Observer\LoginState;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers LoginState
 */
class LoginStateTest extends TestCase
{
    private MockObject $sessionData;
    private LoginState $observer;

    public function testShouldSetUserIdCookie()
    {
        // Expect & Given
        $userId = md5('some user id');
        $this->sessionData->method('getUserId')->willReturn($userId);
        $this->observer->expects($this->once())
            ->method('setCookie')
            ->with('ff_user_id', $userId);
        $this->observer->expects($this->never())->method('clearCookie');

        // When & Then
        $this->observer->execute(new Observer());
    }

    public function testShouldClearUserIdCookie()
    {
        // Expect & Given
        $this->observer->expects($this->once())
            ->method('clearCookie')
            ->with('ff_user_id');
        $this->observer->expects($this->never())->method('setCookie');

        // When & Then
        $this->observer->execute(new Observer());
    }

    protected function setUp(): void
    {
        $this->sessionData = $this->createMock(SessionData::class);
        $sessionConfig  = $this->createMock(SessionConfig::class);
        $this->observer = $this->getMockBuilder(LoginState::class)
            ->setConstructorArgs([
                $this->sessionData,
                $sessionConfig
            ])
            ->onlyMethods(['setCookie', 'clearCookie'])
            ->getMock();
    }
}
