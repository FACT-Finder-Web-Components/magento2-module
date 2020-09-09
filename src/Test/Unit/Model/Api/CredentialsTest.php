<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api;

use PHPUnit\Framework\TestCase;

class CredentialsTest extends TestCase
{
    /** @var Credentials */
    private $credentials;

    public function test_it_correctly_hashes_the_password()
    {
        $this->assertSame('167539c3e7aba8388eee252f429a4a1a', $this->credentials->toArray()['password']);
    }

    public function test_it_provides_valid_credential_data()
    {
        $expected = [
            'timestamp' => 1270732953523,
            'username'  => 'user',
            'password'  => '167539c3e7aba8388eee252f429a4a1a',
        ];
        $this->assertSame($expected, $this->credentials->toArray());
    }

    public function test_it_converts_the_credentials_to_query_string()
    {
        $expected = 'timestamp=1270732953523&username=user&password=167539c3e7aba8388eee252f429a4a1a';
        $this->assertSame($expected, (string) $this->credentials);
    }

    protected function setUp(): void
    {
        $this->credentials = new Credentials('user', 'userpw', 'FACT-FINDER', 'FACT-FINDER');
    }
}

function microtime(): float
{
    return 1270732953.523;
}
