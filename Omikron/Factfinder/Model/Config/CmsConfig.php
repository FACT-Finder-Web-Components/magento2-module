<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Omikron\Factfinder\Api\Config\ChannelProviderInterface;

class CmsConfig implements ChannelProviderInterface
{
    private const PATH_CMS_EXPORT_ENABLED     = 'factfinder/cms_export/ff_cms_export_enabled';
    private const PATH_USE_SEPARATE_CHANNEL   = 'factfinder/cms_export/ff_cms_use_separate_channel';
    private const PATH_ADDITIONAL_CMS_CHANNEL = 'factfinder/cms_export/ff_cms_channel';
    private const PATH_DISABLE_CMS_PAGES      = 'factfinder/cms_export/ff_cms_blacklist';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isCmsExportEnabled(int $scopeCode = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::PATH_CMS_EXPORT_ENABLED, 'store', $scopeCode);
    }

    public function useSeparateCmsChannel(int $scopeCode = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::PATH_USE_SEPARATE_CHANNEL, 'store', $scopeCode);
    }

    public function getCmsBlacklist(int $scopeCode = null): array
    {
        $pages = (string) $this->scopeConfig->getValue(self::PATH_DISABLE_CMS_PAGES, 'store', $scopeCode);
        return array_filter(explode(',', $pages));
    }

    public function getChannel(int $scopeCode = null): string
    {
        return (string) $this->scopeConfig->getValue(self::PATH_ADDITIONAL_CMS_CHANNEL, 'store', $scopeCode);
    }
}
