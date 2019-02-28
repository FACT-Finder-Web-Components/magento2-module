<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api;

class Credentials
{
    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var string */
    private $prefix;

    /** @var string */
    private $postfix;

    public function __construct(string $username, string $password, string $prefix, string $postfix)
    {
        $this->username = $username;
        $this->password = $password;
        $this->prefix   = $prefix;
        $this->postfix  = $postfix;
    }

    public function toArray(): array
    {
        $timestamp = (int) (microtime(true) * 1000);
        return [
            'timestamp' => $timestamp,
            'username'  => $this->username,
            'password'  => md5($this->prefix . $timestamp . md5($this->password) . $this->postfix), // phpcs:ignore
        ];
    }

    public function __toString()
    {
        return http_build_query($this->toArray());
    }
}
