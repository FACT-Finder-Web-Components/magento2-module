<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Model\Export;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Omikron\Factfinder\Model\Export\BasicAuth;
use PHPUnit\Framework\TestCase;

/**
 * @covers BasicAuth
 */
class BasicAuthTest extends TestCase
{
    /** @var BasicAuth */
    private $basicAuth;

    public function test_it_authenticates_the_user_with_valid_credentials()
    {
        $this->assertFalse(
            $this->basicAuth->authenticate('UnknownUser', 'OpenSesame'),
            'User should not be authenticated with a wrong username.'
        );

        $this->assertFalse(
            $this->basicAuth->authenticate('Aladdin', 'WrongPassword'),
            'User should not be authenticated with a wrong password.'
        );

        $this->assertFalse(
            $this->basicAuth->authenticate('UnknownUser', 'WrongPassword'),
            'User should not be authenticated with wrong credentials.'
        );

        $this->assertTrue(
            $this->basicAuth->authenticate('Aladdin', 'OpenSesame'),
            'User should be authenticated with correct credentials.'
        );
    }

    protected function setUp(): void
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturnMap([
            ['factfinder/basic_auth_data_transfer/ff_upload_url_user', 'store', null, 'Aladdin'],
            ['factfinder/basic_auth_data_transfer/ff_upload_url_password', 'store', null, 'OpenSesame'],
        ]);
        $this->basicAuth = new BasicAuth($scopeConfig);
    }
}
