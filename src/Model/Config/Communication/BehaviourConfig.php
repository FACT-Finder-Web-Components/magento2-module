<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config\Communication;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\Config\ParametersSourceInterface;
use Omikron\FactFinder\Communication\Version;

class BehaviourConfig implements ParametersSourceInterface
{
    private const PATH_USE_URL_PARAMETER   = 'factfinder/advanced/use_url_parameter';
    private const PATH_ADD_PARAMS          = 'factfinder/advanced/add_params';
    private const PATH_ADD_TRACKING_PARAMS = 'factfinder/advanced/add_tracking_params';
    private const PATH_KEEP_URL_PARAMS     = 'factfinder/advanced/keep_url_param';
    private const PATH_ONLY_SEARCH_PARAMS  = 'factfinder/advanced/only_search_params';
    private const PATH_PARAMETER_WHITELIST = 'factfinder/advanced/parameter_whitelist';
    private const PATH_VERSION              = 'factfinder/general/version';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getParameters(): array
    {
        $parameters = [
            'use-url-parameters'          => $this->getFlag(self::PATH_USE_URL_PARAMETER),
            'add-params'                  => $this->getConfig(self::PATH_ADD_PARAMS),
            'add-tracking-params'         => $this->getConfig(self::PATH_ADD_TRACKING_PARAMS),
            'keep-url-params'             => $this->getConfig(self::PATH_KEEP_URL_PARAMS),
            'only-search-params'          => $this->getFlag(self::PATH_ONLY_SEARCH_PARAMS),
            'parameter-whitelist'         => $this->getConfig(self::PATH_PARAMETER_WHITELIST),
        ];

        if ($this->getVersion() === Version::NG) $parameters['category-page'] = '';

        return $parameters;
    }

    public function getVersion(): string
    {
        return (string) $this->scopeConfig->getValue(self::PATH_VERSION, ScopeInterface::SCOPE_STORES);
    }

    private function getConfig(string $path): string
    {
        return (string) $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORES);
    }

    private function getFlag(string $path): string
    {
        return $this->scopeConfig->isSetFlag($path, ScopeInterface::SCOPE_STORES) ? 'true' : 'false';
    }
}
