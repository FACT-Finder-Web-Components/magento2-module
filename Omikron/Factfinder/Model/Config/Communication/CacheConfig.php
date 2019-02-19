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
            'use-cache'     => $this->scopeConfig->isSetFlag(self::PATH_USE_CACHE, ScopeInterface::SCOPE_STORES),
            'disable-cache' => $this->scopeConfig->getValue(self::PATH_DISABLE_CACHE, ScopeInterface::SCOPE_STORES),
        ];
    }
}
