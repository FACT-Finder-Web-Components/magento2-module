<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config\Communication;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\Config\ParametersSourceInterface;

class BehaviourConfig implements ParametersSourceInterface
{
    private const PATH_USE_URL_PARAMETER           = 'factfinder/advanced/use_url_parameter';
    private const PATH_ADD_PARAMS                  = 'factfinder/advanced/add_params';
    private const PATH_ADD_TRACKING_PARAMS         = 'factfinder/advanced/add_tracking_params';
    private const PATH_KEEP_URL_PARAMS             = 'factfinder/advanced/keep_url_param';
    private const PATH_DISABLE_SINGLE_HIT_REDIRECT = 'factfinder/advanced/disable_single_hit_redirect';
    private const PATH_ONLY_SEARCH_PARAMS          = 'factfinder/advanced/only_search_params';
    private const PATH_PARAMETER_WHITELIST         = 'factfinder/advanced/parameter_whitelist';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getParameters(): array
    {
        return [
            'use-url-parameter'           => $this->getFlag(self::PATH_USE_URL_PARAMETER),
            'add-params'                  => $this->getConfig(self::PATH_ADD_PARAMS),
            'add-tracking-params'         => $this->getConfig(self::PATH_ADD_TRACKING_PARAMS),
            'keep-url-params'             => $this->getConfig(self::PATH_KEEP_URL_PARAMS),
            'only-search-params'          => $this->getFlag(self::PATH_ONLY_SEARCH_PARAMS),
            'parameter-whitelist'         => $this->getConfig(self::PATH_PARAMETER_WHITELIST),
            'disable-single-hit-redirect' => $this->getFlag(self::PATH_DISABLE_SINGLE_HIT_REDIRECT),
        ];
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
