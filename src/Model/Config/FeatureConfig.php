<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Api\Config\FeatureConfigInterface;

class FeatureConfig implements FeatureConfigInterface
{
    private const PATH_USE_FOR_CATEGORIES = 'factfinder/general/use_for_categories';

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig, CommunicationConfigInterface $communicationConfig)
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
