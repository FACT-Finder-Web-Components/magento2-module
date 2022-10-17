<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Session\Config\ConfigInterface as SessionConfig;
use Omikron\Factfinder\Model\SessionData;
use Omikron\Factfinder\Observer\HasJustLoggedOut;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers HasJustLoggedOut
 */
class HasJustLoggedOutTest extends TestCase
{
    private MockObject $sessionData;
    private HasJustLoggedOut $observer;

    public function testShouldSetCookie()
    {
        // Expect & Given
        $this->observer->expects($this->once())
            ->method('setCookie')
            ->with('ff_has_just_logged_out', '1');
        $this->observer->expects($this->never())->method('clearCookie');

        // When & Then
        $this->observer->execute(new Observer());
    }

    protected function setUp(): void
    {
        $this->sessionData = $this->createMock(SessionData::class);
        $sessionConfig  = $this->createMock(SessionConfig::class);
        $this->observer = $this->getMockBuilder(HasJustLoggedOut::class)
            ->setConstructorArgs([
                $this->sessionData,
                $sessionConfig
            ])
            ->onlyMethods(['setCookie', 'clearCookie'])
            ->getMock();
    }
}
