<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Session\Config\ConfigInterface as SessionConfig;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Omikron\Factfinder\Model\SessionData;
use Omikron\Factfinder\Observer\SetUserCookie;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers SetUserCookie
 */
class SetUserCookieTest extends TestCase
{
    private MockObject $cookieManager;
    private MockObject $cookieMetadata;
    private MockObject $sessionData;
    private SetUserCookie $observer;

    public function test_it_adds_cookies()
    {
        $this->cookieMetadata->expects($this->once())->method('setHttpOnly')->with(false);
        $userId = md5('some user id');
        $hasLoggedIn = 1;
        $this->sessionData->method('getUserId')->willReturn($userId);
        $this->cookieManager->expects($this->exactly(2))->method('setPublicCookie')->withConsecutive(
            ['user_id', $userId, $this->cookieMetadata],
            ['has_just_logged_in', $hasLoggedIn, $this->cookieMetadata]
        );

        $this->observer->execute(new Observer());
    }

    protected function setUp(): void
    {
        $this->cookieManager   = $this->createMock(CookieManagerInterface::class);
        $cookieMetadataFactory = $this->getMockBuilder(CookieMetadataFactory::class)
            ->onlyMethods(['createPublicCookieMetadata'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->cookieMetadata = $this->createMock(PublicCookieMetadata::class);
        $this->cookieMetadata->method('setDuration')->willReturn($this->cookieMetadata);
        $this->cookieMetadata->method('setPath')->willReturn($this->cookieMetadata);
        $this->cookieMetadata->method('setDomain')->willReturn($this->cookieMetadata);
        $this->cookieMetadata->method('setSecure')->willReturn($this->cookieMetadata);
        $this->cookieMetadata->method('setHttpOnly')->willReturn($this->cookieMetadata);
        $cookieMetadataFactory->method('createPublicCookieMetadata')->willReturn($this->cookieMetadata);
        $this->sessionData = $this->createMock(SessionData::class);

        $sessionConfig  = $this->createMock(SessionConfig::class);
        $this->observer = new SetUserCookie(
            $this->cookieManager,
            $cookieMetadataFactory,
            $sessionConfig,
            $this->sessionData
        );
    }
}
