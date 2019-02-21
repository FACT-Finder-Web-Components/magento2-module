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
    private const PATH_SEARCH_IMMEDIATE            = 'factfinder/advanced/search_immediate';
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
            'use-url-parameter'           => $this->scopeConfig->getValue(self::PATH_USE_URL_PARAMETER, ScopeInterface::SCOPE_STORES),
            'add-params'                  => $this->scopeConfig->getValue(self::PATH_ADD_PARAMS, ScopeInterface::SCOPE_STORES),
            'add-tracking-params'         => $this->scopeConfig->getValue(self::PATH_ADD_TRACKING_PARAMS, ScopeInterface::SCOPE_STORES),
            'keep-url-params'             => $this->scopeConfig->getValue(self::PATH_KEEP_URL_PARAMS, ScopeInterface::SCOPE_STORES),
            'only-search-params'          => $this->scopeConfig->isSetFlag(self::PATH_ONLY_SEARCH_PARAMS, ScopeInterface::SCOPE_STORES),
            'parameter-whitelist'         => $this->scopeConfig->getValue(self::PATH_PARAMETER_WHITELIST, ScopeInterface::SCOPE_STORES),
            'search-immediate'            => $this->scopeConfig->isSetFlag(self::PATH_SEARCH_IMMEDIATE, ScopeInterface::SCOPE_STORES),
            'disable-single-hit-redirect' => $this->scopeConfig->getValue(self::PATH_DISABLE_SINGLE_HIT_REDIRECT, ScopeInterface::SCOPE_STORES),
        ];
    }
}
