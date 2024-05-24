<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config\Communication;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\Config\ParametersSourceInterface;

class BehaviourConfig implements ParametersSourceInterface
{
    private const PATH_ADD_PARAMS          = 'factfinder/advanced/add_params';
    private const PATH_PARAMETER_WHITELIST = 'factfinder/advanced/parameter_whitelist';

    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getParameters(): array
    {
        $parameters = [
            'add-params'                  => $this->getConfig(self::PATH_ADD_PARAMS), // postStringifier
            'parameter-whitelist'         => $this->getConfig(self::PATH_PARAMETER_WHITELIST), // allow setUrlParamOptionsListener
        ];

        return $parameters;
    }

    private function getConfig(string $path): string
    {
        return (string) $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORES);
    }
}
