<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\Config\FeatureConfigInterface;

class FeatureConfig implements FeatureConfigInterface
{
    private const CONFIG_PATH_USE_FOR_CATEGORIES = 'factfinder/general/use_for_categories';

    /** @var ScopeConfigInterface */
    private $config;

    public function __construct(ScopeConfigInterface $config)
    {
        $this->config = $config;
    }

    public function useForCategories(): bool
    {
        return $this->config->isSetFlag(self::CONFIG_PATH_USE_FOR_CATEGORIES, ScopeInterface::SCOPE_STORES);
    }
}
