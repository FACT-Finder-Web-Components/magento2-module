<?php

declare(strict_types = 1);

namespace Omikron\Factfinder\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;

class Communication
{
    const PATH_CHANNEL                    = 'factfinder/general/channel';
    const PATH_ADDRESS                    = 'factfinder/general/address';
    const PATH_USERNAME                   = 'factfinder/general/username';
    const PATH_PASSWORD                   = 'factfinder/general/password';
    const PATH_AUTH_PREFIX                = 'factfinder/general/authentication_prefix';
    const PATH_AUTH_POSTFIX               = 'factfinder/general/authentication_postfix';
    const PATH_DATA_TRANSFER_IMPORT       = 'factfinder/data_transfer/ff_push_import_enabled';
    const PATH_DATA_TRANSFER_IMPORT_TYPES = 'factfinder/data_transfer/ff_push_import_type';
    const FF_AUTH_REGISTRY_KEY            = 'ff-auth';

    /** @var ScopeConfigInterface  */
    protected $scopeConfig;

    /** @var Registry  */
    protected $registry;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Registry $registry
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->registry    = $registry;
    }

    public function getAddress() : string
    {
        $registeredAuthData = $this->getRegisteredAuthParams();
        $url = $registeredAuthData['serverUrl'] ?? $this->scopeConfig->getValue(self::PATH_ADDRESS, 'store');

        if (substr(rtrim($url), -1) != '/') {
            $url .= '/';
        }

        return $url;
    }

    public function getChannel(string $storeId = null) : string
    {
        $registeredAuthData = $this->getRegisteredAuthParams();

        return $registeredAuthData['channel'] ?? $this->scopeConfig->getValue(self::PATH_CHANNEL, 'store', $storeId);
    }

    public function getUsername() : string
    {
        $registeredAuthData = $this->getRegisteredAuthParams();

        return $registeredAuthData['username'] ?? $this->scopeConfig->getValue(self::PATH_USERNAME, 'store');
    }

    public function getPassword(): string
    {
        $registeredAuthData = $this->getRegisteredAuthParams();

        return $registeredAuthData['password'] ?? $this->scopeConfig->getValue(self::PATH_PASSWORD, 'store');
    }

    public function getAuthenticationPrefix(): string
    {
        $registeredAuthData = $this->getRegisteredAuthParams();

        return $registeredAuthData['authenticationPrefix'] ?? $this->scopeConfig->getValue(self::PATH_AUTH_PREFIX, 'store');
    }

    public function getAuthenticationPostfix(): string
    {
        $registeredAuthData = $this->getRegisteredAuthParams();

        return $registeredAuthData['authenticationPostfix'] ?? $this->scopeConfig->getValue(self::PATH_AUTH_POSTFIX, 'store');
    }

    /**
     * Checks if automatic import is enabled
     *
     * @param null|int|string $scopeCode
     * @return bool
     */
    public function isPushImportEnabled($scopeCode = null) : bool
    {
        return $this->scopeConfig->isSetFlag(self::PATH_DATA_TRANSFER_IMPORT, 'store', $scopeCode);
    }

    /**
     * Returns data types (Data and/or Suggest) which should be imported on push
     *
     * @param null|int|string $scopeCode
     * @return array
     */
    public function getPushImportTypes($scopeCode = null) : array
    {
        return explode(',', $this->scopeConfig->getValue(self::PATH_DATA_TRANSFER_IMPORT_TYPES, 'store', $scopeCode));
    }

    private function getRegisteredAuthParams(): ?array
    {
        return $this->registry->registry(self::FF_AUTH_REGISTRY_KEY);
    }
}
