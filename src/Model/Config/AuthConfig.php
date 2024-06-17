<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface as Scope;

class AuthConfig
{
    private const PATH_USERNAME = 'factfinder/data_transfer/ff_username';
    private const PATH_PASSWORD = 'factfinder/data_transfer/ff_password';
    private const PATH_API_KEY  = 'factfinder/general/ff_api_key';

    private ScopeConfigInterface $scopeConfig;

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

    public function getApiKey(): string
    {
        return (string) $this->scopeConfig->getValue(self::PATH_API_KEY, Scope::SCOPE_STORE);
    }
}
