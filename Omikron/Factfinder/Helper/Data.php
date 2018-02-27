<?php

namespace Omikron\Factfinder\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Data
 * Helper class to get the configuration of the factfinder module
 * @package Omikron\Factfinder\Helper
 */
class Data extends AbstractHelper
{
    const FRONT_NAME = "FACT-Finder";
    const CUSTOM_RESULT_PAGE = "result";
    const SESSION_ID_LENGTH = 30;

    const PATH_TRACKING_PRODUCT_NUMBER_FIELD_ROLE = 'factfinder/general/tracking_product_number_field_role';
    const PATH_IS_ENABLED = 'factfinder/general/is_enabled';
    const PATH_IS_ENRICHMENT_ENABLED = 'factfinder/general/ff_enrichment';
    const PATH_ADDRESS = 'factfinder/general/address';
    const PATH_CHANNEL = 'factfinder/general/channel';
    const PATH_USERNAME = 'factfinder/general/username';
    const PATH_PASSWORD = 'factfinder/general/password';
    const PATH_SHOW_ADD_TO_CART_BUTTON = 'factfinder/general/show_add_to_card_button';
    const PATH_AUTH_PREFIX = 'factfinder/general/authentication_prefix';
    const PATH_AUTH_POSTFIX = 'factfinder/general/authentication_postfix';
    const PATH_ADVANCED_VERSION = 'factfinder/advanced/version';
    const PATH_DATATRANSFER_IMPORT = 'factfinder/data_transfer/ff_cron_import';

    /** @var \Magento\Config\Model\ResourceModel\Config */
    protected $_resourceConfig;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig
    )
    {
        parent::__construct($context);
        $this->_resourceConfig = $resourceConfig;
    }

    /**
     * Public Getter
     * @param null|int|string $scopeCode
     * @return bool
     */
    public function isEnabled($scopeCode = null)
    {
        return boolval($this->scopeConfig->getValue(self::PATH_IS_ENABLED, 'store', $scopeCode));
    }

    /**
     * Public Getter
     * @return bool
     */
    public function isEnrichmentEnabled()
    {
        return boolval($this->scopeConfig->getValue(self::PATH_IS_ENRICHMENT_ENABLED, 'store'));
    }

    /**
     * Returns URL
     * @return mixed
     */
    public function getAddress()
    {
        $url = $this->scopeConfig->getValue(self::PATH_ADDRESS, 'store');

        if (substr(rtrim($url), -1) != "/") {
            $url .= "/";
        }

        return $url;
    }

    /**
     * Returns the FACT-Finder channel name
     * @param null|int|string $scopeCode
     * @return string
     */
    public function getChannel($scopeCode = null)
    {
        return $this->scopeConfig->getValue(self::PATH_CHANNEL, 'store', $scopeCode);
    }

    /**
     * Returns pushImport Setting
     * @param null|int|string $scopeCode
     * @return bool
     */
    public function isPushImportEnabled($scopeCode = null)
    {
        return boolval($this->scopeConfig->getValue(self::PATH_DATATRANSFER_IMPORT, 'store', $scopeCode));
    }

    /**
     * Returns the specific fields used as tracking id
     * @param string $fieldRoleName
     * @return string
     */
    public function getFieldRole($fieldRoleName)
    {
        $fr = json_decode($this->getFieldRoles(), true);
        if(is_array($fr) && array_key_exists($fieldRoleName, $fr)) {
            return $fr[$fieldRoleName];
        } else {
            return "";
        }
    }

    /**
     * Returns all fields used as tracking id
     * @return string
     */
    public function getFieldRoles()
    {
        return $this->scopeConfig->getValue(self::PATH_TRACKING_PRODUCT_NUMBER_FIELD_ROLE, 'store');
    }

    /**
     * Returns the FACT-Finder username
     * @return mixed
     */
    public function getUsername()
    {
        return $this->scopeConfig->getValue(self::PATH_USERNAME, 'store');
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
     * Returns the keep-filters configuration
     * @return mixed
     */
    public function getKeepFilters()
    {
        return $this->scopeConfig->getValue('factfinder/advanced/keep_filters', 'store');
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

    /**
     * Private Getter
     */

    /**
     * Returns the FACT-Finder password
     * @return mixed
     */
    private function getPassword()
    {
        return $this->scopeConfig->getValue(self::PATH_PASSWORD, 'store');
    }

    /**
     * Returns the authentication prefix
     * @return mixed
     */
    private function getAuthenticationPrefix()
    {
        return $this->scopeConfig->getValue(self::PATH_AUTH_PREFIX, 'store');
    }

    /**
     * Returns the authentication postfix
     * @return mixed
     */
    private function getAuthenticationPostfix()
    {
        return $this->scopeConfig->getValue(self::PATH_AUTH_POSTFIX, 'store');
    }

    /**
     * Returns the authentication values as array
     *
     * @return array
     */
    public function getAuthArray()
    {
        $authArray = [];
        $authArray['username'] = $this->getUsername();

        $time = round(microtime(true) * 1000);
        $password = $this->getPassword();
        $prefix = $this->getAuthenticationPrefix();
        $postfix = $this->getAuthenticationPostfix();

        $hashPassword = md5($prefix . (string)$time . md5($password) . $postfix);

        $authArray['password'] = $hashPassword;
        $authArray['timestamp'] = $time;

        return $authArray;
    }

    /**
     * Set field roles
     * @param string $value
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return mixed
     */
    public function setFieldRoles($value, $store)
    {
        return $this->_resourceConfig->saveConfig(self::PATH_TRACKING_PRODUCT_NUMBER_FIELD_ROLE, $value, 'stores', $store->getId());
    }

    /**
     * Get correct sessionId
     * @param string $sessionId
     * @return string
     */
    public function getCorrectSessionId($sessionId)
    {
        while (strlen($sessionId) !== self::SESSION_ID_LENGTH) {
            if (strlen($sessionId) < self::SESSION_ID_LENGTH) {
                $sessionId = $sessionId . substr($sessionId, 0, (self::SESSION_ID_LENGTH - strlen($sessionId)));
            } else if (strlen($sessionId) > self::SESSION_ID_LENGTH) {
                $sessionId = substr($sessionId, 0, self::SESSION_ID_LENGTH);
            }
        }

        return $sessionId;
    }
}
