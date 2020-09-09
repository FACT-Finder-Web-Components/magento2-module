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

    public function __construct(string $username, string $password, string $prefix = null, string $postfix = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->prefix   = $prefix;
        $this->postfix  = $postfix;
    }

    public function toBasicAuth(): string
    {
        return 'Basic ' . base64_encode("$this->username:$this->password");
    }

    public function toArray(): array
    {
        $timestamp = (int) (microtime(true) * 1000);

        return [
            'timestamp' => $timestamp,
            'username'  => $this->username,
            // phpcs:ignore Magento2.Security.InsecureFunction
            'password'  => md5($this->prefix . $timestamp . md5($this->password) . $this->postfix),
        ];
    }

    public function __toString(): string
    {
        return http_build_query($this->toArray());
    }
}
