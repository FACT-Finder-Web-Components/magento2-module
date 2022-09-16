<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Session\Config\ConfigInterface as SessionConfig;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Omikron\Factfinder\Observer\SetUserHasLoggedIn;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers SetUserHasLoggedIn
 */
class SetUserHasLoggedInTest extends TestCase
{
    private MockObject $cookieManager;
    private MockObject $cookieMetadata;
    private SetUserHasLoggedIn $observer;

    public function test_it_adds_cookie()
    {
        $this->cookieMetadata->expects($this->once())->method('setHttpOnly')->with(false);
        $this->cookieManager->expects($this->once())->method('setPublicCookie')
            ->with('has_just_logged_in', 1, $this->cookieMetadata);
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

        $sessionConfig  = $this->createMock(SessionConfig::class);
        $this->observer = new SetUserHasLoggedIn(
            $this->cookieManager,
            $cookieMetadataFactory,
            $sessionConfig
        );
    }
}
