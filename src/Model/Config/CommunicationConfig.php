<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\Config\ParametersSourceInterface;
use Omikron\FactFinder\Communication\Version;
use Omikron\Factfinder\Controller\Router;

class CommunicationConfig implements ParametersSourceInterface
{
    private const PATH_CHANNEL              = 'factfinder/general/channel';
    private const PATH_ADDRESS              = 'factfinder/general/address';
    private const PATH_VERSION              = 'factfinder/general/version';
    private const PATH_API_VERSION          = 'factfinder/general/ff_api_version';
    private const PATH_IS_ENABLED           = 'factfinder/general/is_enabled';
    private const PATH_USE_PROXY            = 'factfinder/general/ff_enrichment';
    private const PATH_DATA_TRANSFER_IMPORT = 'factfinder/data_transfer/ff_push_import_enabled';
    private const PATH_IS_LOGGING_ENABLED   = 'factfinder/general/logging_enabled';
    private const PATH_FF_API_KEY           = 'factfinder/general/ff_api_key';

    private ScopeConfigInterface $scopeConfig;

    private UrlInterface $urlBuilder;

    public function __construct(ScopeConfigInterface $scopeConfig, UrlInterface $urlBuilder)
    {
        $this->scopeConfig = $scopeConfig;
        $this->urlBuilder  = $urlBuilder;
    }

    public function getChannel(int $scopeId = null): string
    {
        return (string) $this->scopeConfig->getValue(self::PATH_CHANNEL, ScopeInterface::SCOPE_STORES, $scopeId);
    }

    public function getAddress(): string
    {
        return (string) $this->scopeConfig->getValue(self::PATH_ADDRESS, ScopeInterface::SCOPE_STORES);
    }

    public function isChannelEnabled(int $scopeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::PATH_IS_ENABLED, ScopeInterface::SCOPE_STORES, $scopeId);
    }

    public function isPushImportEnabled(int $scopeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::PATH_DATA_TRANSFER_IMPORT, ScopeInterface::SCOPE_STORES, $scopeId);
    }

    public function getVersion(): string
    {
        return (string) $this->scopeConfig->getValue(self::PATH_VERSION, ScopeInterface::SCOPE_STORES);
    }

    public function getApiKey(): string
    {
        return (string) $this->scopeConfig->getValue(self::PATH_FF_API_KEY, ScopeInterface::SCOPE_STORES);
    }

    public function isLoggingEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::PATH_IS_LOGGING_ENABLED, ScopeInterface::SCOPE_STORES);
    }

    public function getParameters(): array
    {
        return [
            'url'     => $this->getServerUrl(),
            'channel' => $this->getChannel(),
            'api_key' => $this->getApiKey(),
        ];
    }

    public function getApiVersion(): string
    {
        return (string) $this->scopeConfig->getValue(self::PATH_API_VERSION, ScopeInterface::SCOPE_STORES) ?? 'v4';
    }

    private function getServerUrl(): string
    {
        if ($this->scopeConfig->isSetFlag(self::PATH_USE_PROXY, ScopeInterface::SCOPE_STORES)) {
            return $this->urlBuilder->getUrl('', ['_direct' => Router::FRONT_NAME]);
        }

        return $this->getAddress();
    }
}
