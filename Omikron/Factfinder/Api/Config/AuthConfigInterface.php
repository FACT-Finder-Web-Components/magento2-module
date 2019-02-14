<?php

namespace Omikron\Factfinder\Api\Config;

interface AuthConfigInterface
{
    public function getUsername(): string;

    public function getPassword(): string;

    public function getAuthenticationPrefix(): string;

    public function getAuthenticationPostfix(): string;
}
