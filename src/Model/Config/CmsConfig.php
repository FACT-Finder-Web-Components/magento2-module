<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

class CmsConfig
{
    private const PATH_DISABLE_CMS_PAGES = 'factfinder/cms_export/ff_cms_blacklist';

    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getCmsBlacklist(int $scopeCode = null): array
    {
        $pages = (string) $this->scopeConfig->getValue(self::PATH_DISABLE_CMS_PAGES, 'store', $scopeCode);
        return array_filter(explode(',', $pages));
    }
}
