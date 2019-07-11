<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config\Communication;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\Config\ParametersSourceInterface;

class CacheConfig implements ParametersSourceInterface
{
    private const PATH_USE_CACHE     = 'factfinder/advanced/use_cache';
    private const PATH_DISABLE_CACHE = 'factfinder/advanced/disable_cache';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getParameters(): array
    {
        return [
            'use-cache'     => $this->getFlag(self::PATH_USE_CACHE),
            'disable-cache' => $this->getFlag(self::PATH_DISABLE_CACHE),
        ];
    }

    private function getFlag(string $path): string
    {
        return $this->scopeConfig->isSetFlag($path, ScopeInterface::SCOPE_STORES) ? 'true' : 'false';
    }
}
