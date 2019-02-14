<?php

namespace Omikron\Factfinder\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Data
 * Helper class to get the configuration of the factfinder module
 */
class Data extends AbstractHelper
{
    const FRONT_NAME         = 'FACT-Finder';
    const EXPORT_PAGE        = 'export';
    const CUSTOM_RESULT_PAGE = 'result';

    const PATH_IS_ENABLED                   = 'factfinder/general/is_enabled';
    const PATH_IS_ENRICHMENT_ENABLED        = 'factfinder/general/ff_enrichment';
    const PATH_SHOW_ADD_TO_CART_BUTTON      = 'factfinder/general/show_add_to_card_button';
    const PATH_ADVANCED_VERSION             = 'factfinder/advanced/version';
    const PATH_DATA_TRANSFER_IMPORT         = 'factfinder/data_transfer/ff_push_import_enabled';
    const PATH_CONFIGURABLE_CRON_IS_ENABLED = 'factfinder/configurable_cron/ff_cron_enabled';

    /**
     * Public Getter
     * @param null|int|string $scopeCode
     * @return bool
     */
    public function isEnabled($scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag(self::PATH_IS_ENABLED, 'store', $scopeCode);
    }

    /**
     * Public Getter
     * @return bool
     */
    public function isEnrichmentEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::PATH_IS_ENRICHMENT_ENABLED, 'store');
    }

    /**
     * Checks if automatic import is enabled
     *
     * @param null|int|string $scopeCode
     * @return bool
     */
    public function isPushImportEnabled($scopeCode = null) : bool
    {
        return $this->scopeConfig->isSetFlag(self::PATH_DATA_TRANSFER_IMPORT, 'store', $scopeCode);
    }

    /**
     * Returns the show_add_to_card_button configuration
     * @return mixed
     */
    public function getShowAddToCartButton()
    {
        return $this->scopeConfig->getValue(self::PATH_SHOW_ADD_TO_CART_BUTTON, 'store');
    }

    /**
     * Returns the current FACT-Finder version
     * @param null|int|string $scopeCode
     * @return mixed
     */
    public function getVersion($scopeCode = null)
    {
        return $this->scopeConfig->getValue(self::PATH_ADVANCED_VERSION, 'store', $scopeCode);
    }

    /**
     * Returns the search-immediate configuration
     * @return mixed
     */
    public function getSearchImmediate()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/search_immediate', 'store');
    }

    /**
     * Returns the disable-single-hit-redirect configuration
     * @return mixed
     */
    public function getDisableSingleHitRedirect()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/disable_single_hit_redirect', 'store');
    }

    /**
     * Returns the use-url-parameter configuration
     * @return mixed
     */
    public function getUseUrlParameter()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/use_url_parameter', 'store');
    }

    /**
     * Returns the use-cache configuration
     * @return mixed
     */
    public function getUseCache()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/use_cache', 'store');
    }

    /**
     * Returns the default-query configuration
     * @return mixed
     */
    public function getDefaultQuery()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/default_query', 'store');
    }

    /**
     * Returns the add-params configuration
     * @return mixed
     */
    public function getAddParams()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/add_params', 'store');
    }

    /**
     * Returns the add-tracking-params configuration
     * @return mixed
     */
    public function getAddTrackingParams()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/add_tracking_params', 'store');
    }

    /**
     * Returns the keep-url-params configuration
     * @return mixed
     */
    public function getKeepUrlParams()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/keep_url_params', 'store');
    }

    /**
     * Returns the use-asn configuration
     * @return mixed
     */
    public function getUseAsn()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/use_asn', 'store');
    }

    /**
     * Returns the use-found-words configuration
     * @return mixed
     */
    public function getUseFoundWords()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/use_found_words', 'store');
    }

    /**
     * Returns the use-campaigns configuration
     * @return mixed
     */
    public function getUseCampaigns()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/use_campaigns', 'store');
    }

    /**
     * Returns the generate-advisor-tree configuration
     * @return mixed
     */
    public function getGenerateAdvisorTree()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/generate_advisor_tree', 'store');
    }

    /**
     * Returns the disable-cache configuration
     * @return mixed
     */
    public function getDisableCache()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/disable_cache', 'store');
    }

    /**
     * Returns the use-personalization configuration
     * @return mixed
     */
    public function getUsePersonalization()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/use_personalization', 'store');
    }

    /**
     * Returns the use-semantic-enhancer configuration
     * @return mixed
     */
    public function getUseSemanticEnhancer()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/use_semantic_enhancer', 'store');
    }

    /**
     * Returns the use-aso configuration
     * @return mixed
     */
    public function getUseAso()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/use_aso', 'store');
    }

    /**
     * Returns the use-browser-history configuration
     * @return mixed
     */
    public function getUseBrowserHistory()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/use_browser_history', 'store');
    }

    /**
     * Returns the use-seo configuration
     * @return mixed
     */
    public function getUseSeo()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/use_seo', 'store');
    }

    /**
     * Returns the seo-prefix configuration
     * @return mixed
     */
    public function getSeoPrefix()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/seo_prefix', 'store');
    }

    public function getFFSuggest()
    {
        return $this->scopeConfig->getValue('factfinder/components/ff_suggest', 'store');
    }

    public function getFFAsn()
    {
        return $this->scopeConfig->getValue('factfinder/components/ff_asn', 'store');
    }

    public function getFFPaging()
    {
        return $this->scopeConfig->getValue('factfinder/components/ff_paging', 'store');
    }

    public function getFFSortbox()
    {
        return $this->scopeConfig->getValue('factfinder/components/ff_sortbox', 'store');
    }

    public function getFFBreadcrumb()
    {
        return $this->scopeConfig->getValue('factfinder/components/ff_breadcrumb', 'store');
    }

    public function getFFProductspp()
    {
        return $this->scopeConfig->getValue('factfinder/components/ff_productspp', 'store');
    }

    public function getFFCampaign()
    {
        return $this->scopeConfig->getValue('factfinder/components/ff_campaign', 'store');
    }

    public function getFFPushedproductscampaign()
    {
        return $this->scopeConfig->getValue('factfinder/components/ff_pushedproductscampaign', 'store');
    }

    public function isCronEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::PATH_CONFIGURABLE_CRON_IS_ENABLED);
    }
}
