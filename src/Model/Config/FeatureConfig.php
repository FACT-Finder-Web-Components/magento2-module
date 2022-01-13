<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class FeatureConfig
{
    private const PATH_USE_FOR_CATEGORIES = 'factfinder/general/use_for_categories';

    private CommunicationConfig $communicationConfig;
    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig, CommunicationConfig $communicationConfig)
    {
        $this->scopeConfig         = $scopeConfig;
        $this->communicationConfig = $communicationConfig;
    }

    public function useForCategories(): bool
    {
        return $this->communicationConfig->isChannelEnabled()
            && $this->scopeConfig->isSetFlag(self::PATH_USE_FOR_CATEGORIES, ScopeInterface::SCOPE_STORES);
    }
}
