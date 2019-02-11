<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Omikron\Factfinder\Api\Config\AuthConfigInterface;

class AuthConfig implements AuthConfigInterface
{
    const PATH_USERNAME     = 'factfinder/general/username';
    const PATH_PASSWORD     = 'factfinder/general/password';
    const PATH_AUTH_PREFIX  = 'factfinder/general/authentication_prefix';
    const PATH_AUTH_POSTFIX = 'factfinder/general/authentication_postfix';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getUsername(): string
    {
        return $this->scopeConfig->getValue(self::PATH_USERNAME);
    }

    public function getPassword(): string
    {
        return $this->scopeConfig->getValue(self::PATH_PASSWORD);
    }

    public function getAuthenticationPrefix(): string
    {
        return $this->scopeConfig->getValue(self::PATH_AUTH_PREFIX);
    }

    public function getAuthenticationPostfix(): string
    {
        return $this->scopeConfig->getValue(self::PATH_AUTH_POSTFIX);
    }
}
