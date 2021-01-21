<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface as Scope;
use Omikron\Factfinder\Api\Config\AuthConfigInterface;

class AuthConfig implements AuthConfigInterface
{
    private const PATH_USERNAME     = 'factfinder/general/username';
    private const PATH_PASSWORD     = 'factfinder/general/password';
    private const PATH_AUTH_PREFIX  = 'factfinder/general/prefix';
    private const PATH_AUTH_POSTFIX = 'factfinder/general/postfix';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getUsername(): string
    {
        return (string) $this->scopeConfig->getValue(self::PATH_USERNAME, Scope::SCOPE_STORE);
    }

    public function getPassword(): string
    {
        return (string) $this->scopeConfig->getValue(self::PATH_PASSWORD, Scope::SCOPE_STORE);
    }

    public function getAuthenticationPrefix(): string
    {
        return (string) $this->scopeConfig->getValue(self::PATH_AUTH_PREFIX, Scope::SCOPE_STORE);
    }

    public function getAuthenticationPostfix(): string
    {
        return (string) $this->scopeConfig->getValue(self::PATH_AUTH_POSTFIX, Scope::SCOPE_STORE);
    }
}
