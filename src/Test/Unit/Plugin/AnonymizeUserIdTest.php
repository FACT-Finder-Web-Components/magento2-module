<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Omikron\Factfinder\Model\SessionData;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Plugin\AnonymizeUserId;
use PHPUnit\Framework\TestCase;

/**
 * @covers AnonymizeUserId
 */
class AnonymizeUserIdTest extends TestCase
{
    /** @var ScopeConfigInterface */
    private $scopeConfigMock;

    /** @var AnonymizeUserId */
    private $plugin;

    public function test_hash_user_id_if_option_is_enabled()
    {
        $this->scopeConfigMock
            ->method('isSetFlag')
            ->with('factfinder/advanced/anonymize_user_id', ScopeInterface::SCOPE_STORES, null)
            ->willReturn(true);
        $userId = '1234';
        $hashed = md5($userId); //phpcs:ignore

        $this->assertSame($hashed, $this->plugin->afterGetUserId($this->createMock(SessionData::class), $userId));
    }

    public function test_does_not_hash_user_id_if_option_is_disabled()
    {
        $this->scopeConfigMock
            ->method('isSetFlag')
            ->with('factfinder/advanced/anonymize_user_id', ScopeInterface::SCOPE_STORES, null)
            ->willReturn(false);
        $userId = '1234';

        $this->assertSame($userId, $this->plugin->afterGetUserId($this->createMock(SessionData::class), $userId));
    }

    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->plugin = new AnonymizeUserId($this->scopeConfigMock);
    }
}
