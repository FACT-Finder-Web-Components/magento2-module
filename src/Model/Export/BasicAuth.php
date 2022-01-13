<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class BasicAuth
{
    private const CONFIG_PATH_USERNAME = 'factfinder/basic_auth_data_transfer/ff_upload_url_user';
    private const CONFIG_PATH_PASSWORD = 'factfinder/basic_auth_data_transfer/ff_upload_url_password';
    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function authenticate(string $username, string $password): bool
    {
        return strcmp($username, $this->getUsername()) === 0 && strcmp($password, $this->getPassword()) === 0;
    }

    private function getUsername(): string
    {
        return (string) $this->scopeConfig->getValue(self::CONFIG_PATH_USERNAME, ScopeInterface::SCOPE_STORE);
    }

    private function getPassword(): string
    {
        return (string) $this->scopeConfig->getValue(self::CONFIG_PATH_PASSWORD, ScopeInterface::SCOPE_STORE);
    }
}
