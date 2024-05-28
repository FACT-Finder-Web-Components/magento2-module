<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config\Communication;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\Config\ParametersSourceInterface;

class BehaviourConfig implements ParametersSourceInterface
{
    private const PATH_ADD_PARAMS          = 'factfinder/advanced/add_params';
    private const PATH_PARAMETER_WHITELIST = 'factfinder/advanced/parameter_whitelist';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly Json $serializer
    ) {
    }

    public function getParameters(): array
    {
        $parameters = [
            'add-params'          => $this->getAddParams(),
            'parameter-whitelist' => $this->getParameterWhitelist(),
        ];

        return $parameters;
    }

    private function getConfig(string $path): string
    {
        return (string) $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORES);
    }

    private function getAddParams(): array
    {
        $storedValue  = $this->scopeConfig->getValue(self::PATH_ADD_PARAMS);
        $unserialized = array_values($this->serializer->unserialize($storedValue ?: '[]'));

        return array_column($unserialized, 'value', 'name');
    }

    private function getParameterWhitelist(): string
    {
        $storedValue  = $this->scopeConfig->getValue(self::PATH_PARAMETER_WHITELIST);
        $unserialized = array_values($this->serializer->unserialize($storedValue ?: '[]'));
        $params = array_column($unserialized, 'name');
        array_walk($params, fn(&$param) => $param = "'$param'");

        return rtrim(implode(', ', $params));
    }
}
