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

    const PATH_SHOW_ADD_TO_CART_BUTTON      = 'factfinder/general/show_add_to_card_button';
    const PATH_ADVANCED_VERSION             = 'factfinder/advanced/version';
    const PATH_DATA_TRANSFER_IMPORT         = 'factfinder/data_transfer/ff_push_import_enabled';
    const PATH_CONFIGURABLE_CRON_IS_ENABLED = 'factfinder/configurable_cron/ff_cron_enabled';

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
     * Returns the disable-single-hit-redirect configuration
     * @return mixed
     */
    public function getDisableSingleHitRedirect()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/disable_single_hit_redirect', 'store');
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
