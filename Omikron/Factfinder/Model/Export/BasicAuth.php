<?php

namespace Omikron\Factfinder\Model\Export;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class BasicAuth
{
    /** @var string */
    private $basePath = 'factfinder/basic_auth_data_transfer/ff_upload_url_';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

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
        return (string) $this->scopeConfig->getValue($this->basePath . 'user', ScopeInterface::SCOPE_STORE);
    }

    private function getPassword(): string
    {
        return (string) $this->scopeConfig->getValue($this->basePath . 'password', ScopeInterface::SCOPE_STORE);
    }
}
