<?php

namespace Omikron\Factfinder\Test\Unit\Helper\Data;

use Magento\Store\Api\Data\StoreInterface;
use \Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\App\Helper\Context;
use \Magento\Config\Model\ResourceModel\Config;
use Omikron\Factfinder\Helper\Data;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Omikron\Factfinder\Helper\Data
     */
    protected $data;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $resourceConfig;

    public function setUp()
    {
        $map = [
            ['factfinder/general/is_enabled', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/general/address', ScopeInterface::SCOPE_STORE, null, 'http://example.com'],
            ['factfinder/general/channel', ScopeInterface::SCOPE_STORE, null, 'magento2-dev'],
            ['factfinder/general/username', ScopeInterface::SCOPE_STORE, null, 'username'],
            ['factfinder/general/password', ScopeInterface::SCOPE_STORE, null, 'password'],
            ['factfinder/general/authentication_prefix', ScopeInterface::SCOPE_STORE, null, 'FF_PREFIX'],
            ['factfinder/general/authentication_postfix', ScopeInterface::SCOPE_STORE, null, 'FF_POSTFIX'],
            ['factfinder/general/show_add_to_card_button', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/general/ff_enrichment', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/components/ff_suggest', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/components/ff_asn', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/components/ff_paging', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/components/ff_sortbox', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/components/ff_breadcrumb', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/components/ff_productspp', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/components/ff_campaign', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/components/ff_pushedproductscampaign', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/advanced/version', ScopeInterface::SCOPE_STORE, null, '7.3'],
            ['factfinder/advanced/search_immediate', ScopeInterface::SCOPE_STORE, null, 'search-immediate'],
            ['factfinder/advanced/use_url_parameter', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/advanced/use_cache', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/advanced/default_query', ScopeInterface::SCOPE_STORE, null, '*'],
            ['factfinder/advanced/add_params', ScopeInterface::SCOPE_STORE, null, 'param1=abc,param2=xyz'],
            ['factfinder/advanced/add_tracking_params', ScopeInterface::SCOPE_STORE, null, 'param1=abc,param2=xyz'],
            ['factfinder/advanced/keep_filters', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/advanced/keep_url_params', ScopeInterface::SCOPE_STORE, null, 'keep-url-params'],
            ['factfinder/advanced/use_asn', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/advanced/use_found_words', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/advanced/use_campaigns', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/advanced/generate_advisor_tree', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/advanced/disable_cache', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/advanced/use_personalization', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/advanced/use_semantic_enhancer', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/advanced/use_aso', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/advanced/use_browser_history', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/advanced/use_seo', ScopeInterface::SCOPE_STORE, null, 1],
            ['factfinder/advanced/seo_prefix', ScopeInterface::SCOPE_STORE, null, 'domain.com/prefix/seoPath'],
            ['factfinder/data_transfer/ff_cron_import', ScopeInterface::SCOPE_STORE, null, 1],
            [
                'factfinder/general/tracking_product_number_field_role',
                ScopeInterface::SCOPE_STORE,
                null,
                '{"brand":"Manufacturer","campaignProductNumber":"ArticleNumber","deeplink":"Deeplink",'
                . '"description":"Description","displayProductNumber":"ArticleNumber","ean":"EAN",'
                . '"imageUrl":"ImageName","masterArticleNumber":"MasterArticleNumber","price":"Price"'
                . ',"productName":"Title","trackingProductNumber":"ArticleNumber"}'
            ]
        ];

        $scopeConfig = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $scopeConfig->expects($this->any())
            ->method('getValue')
            ->will($this->returnValueMap($map));
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $context->method('getScopeConfig')
            ->willReturn($scopeConfig);
        $this->resourceConfig = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resourceConfig->method('saveConfig')
            ->willReturn($this->resourceConfig);

        $this->data = new Data($context, $this->resourceConfig);
    }

    public function testIsEnabled()
    {
        $this->assertEquals(true, $this->data->isEnabled());
    }

    public function testGetAddress()
    {
        $this->assertEquals('http://example.com/', $this->data->getAddress());
    }

    public function testGetChannel()
    {
        $this->assertEquals('magento2-dev', $this->data->getChannel());
    }

    public function testGetUsername()
    {
        $this->assertEquals('username', $this->data->getUsername());
    }

    public function testGetShowAddToCartButton()
    {
        $this->assertEquals(true, $this->data->getShowAddToCartButton());
    }

    public function testisEnrichmentEnabled()
    {
        $this->assertEquals(true, $this->data->isEnrichmentEnabled());
    }

    public function testGetFFSuggest()
    {
        $this->assertEquals(true, $this->data->getFFSuggest());
    }

    public function testGetFFAsn()
    {
        $this->assertEquals(true, $this->data->getFFAsn());
    }

    public function testGetFFPaging()
    {
        $this->assertEquals(true, $this->data->getFFPaging());
    }

    public function testGetFFSortBox()
    {
        $this->assertEquals(true, $this->data->getFFSortBox());
    }

    public function testGetFFBreadcrumb()
    {
        $this->assertEquals(true, $this->data->getFFBreadcrumb());
    }

    public function testGetFFProductspp()
    {
        $this->assertEquals(true, $this->data->getFFProductspp());
    }

    public function testGetFFCampaign()
    {
        $this->assertEquals(true, $this->data->getFFCampaign());
    }

    public function testGetFFPushedproductscampaign()
    {
        $this->assertEquals(true, $this->data->getFFPushedproductscampaign());
    }

    public function testGetVersion()
    {
        $this->assertEquals('7.3', $this->data->getVersion());
    }

    public function testSearchImmediate()
    {
        $this->assertEquals('search-immediate', $this->data->getSearchImmediate());
    }

    public function testGetUseUrlParameter()
    {
        $this->assertEquals(true, $this->data->getUseUrlParameter());
    }

    public function testGetUseCache()
    {
        $this->assertEquals(true, $this->data->getUseCache());
    }

    public function testGetDefaultQuery()
    {
        $this->assertEquals('*', $this->data->getDefaultQuery());
    }

    public function testGetAddParams()
    {
        $this->assertEquals('param1=abc,param2=xyz', $this->data->getAddParams());
    }

    public function testGetAddTrackingParams()
    {
        $this->assertEquals('param1=abc,param2=xyz', $this->data->getAddTrackingParams());
    }

    public function testGetKeepFilters()
    {
        $this->assertEquals(true, $this->data->getKeepFilters());
    }

    public function testGetKeepUrlParams()
    {
        $this->assertEquals('keep-url-params', $this->data->getKeepUrlParams());
    }

    public function testGetUseAsn()
    {
        $this->assertEquals(true, $this->data->getUseAsn());
    }

    public function testGetUseFoundWords()
    {
        $this->assertEquals(true, $this->data->getUseFoundWords());
    }

    public function testGetUseCampaigns()
    {
        $this->assertEquals(true, $this->data->getUseCampaigns());
    }

    public function testGetGenerateAdivsorTree()
    {
        $this->assertEquals(true, $this->data->getGenerateAdvisorTree());
    }

    public function testGetDisableCache()
    {
        $this->assertEquals(true, $this->data->getDisableCache());
    }

    public function testGetUsePersonalization()
    {
        $this->assertEquals(true, $this->data->getUsePersonalization());
    }

    public function testGetUseSemanticEnhancer()
    {
        $this->assertEquals(true, $this->data->getUseSemanticEnhancer());
    }

    public function testGetUseAso()
    {
        $this->assertEquals(true, $this->data->getUseAso());
    }

    public function testGetUseBrowserHistory()
    {
        $this->assertEquals(true, $this->data->getUseBrowserHistory());
    }

    public function testGetUseSeo()
    {
        $this->assertEquals(true, $this->data->getUseSeo());
    }

    public function testGetSeoPrefix()
    {
        $this->assertEquals('domain.com/prefix/seoPath', $this->data->getSeoPrefix());
    }

    public function testIsPushImportEnabled()
    {
        $this->assertEquals(true, $this->data->isPushImportEnabled());
    }

    public function testGetAuthArray()
    {
        $authArray = $this->data->getAuthArray();

        $this->assertInternalType('array', $authArray);
        $this->assertArrayHasKey('password', $authArray);
        $this->assertArrayHasKey('timestamp', $authArray);
        $this->assertNotNull($authArray['password']);
        $this->assertNotNull($authArray['timestamp']);
        $this->assertRegExp('/^[a-f0-9]{32}$/i', $authArray['password']);
        $this->assertTrue(is_numeric($authArray['timestamp']));
    }

    public function testGetFieldRole()
    {
        $this->assertEquals('Manufacturer', $this->data->getFieldRole('brand'));
        $this->assertEquals('ArticleNumber', $this->data->getFieldRole('campaignProductNumber'));
        $this->assertEquals('Deeplink', $this->data->getFieldRole('deeplink'));
        $this->assertEquals('Description', $this->data->getFieldRole('description'));
        $this->assertEquals('ArticleNumber', $this->data->getFieldRole('displayProductNumber'));
        $this->assertEquals('EAN', $this->data->getFieldRole('ean'));
        $this->assertEquals('ImageName', $this->data->getFieldRole('imageUrl'));
        $this->assertEquals('MasterArticleNumber', $this->data->getFieldRole('masterArticleNumber'));
        $this->assertEquals('Price', $this->data->getFieldRole('price'));
        $this->assertEquals('Title', $this->data->getFieldRole('productName'));
        $this->assertEquals('ArticleNumber', $this->data->getFieldRole('trackingProductNumber'));

        $this->assertEquals('', $this->data->getFieldRole('some-unknown-field'));
    }

    public function testSetFieldRoles()
    {
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMock();
        $store->method('getId')
            ->willReturn(1);

        $this->assertEquals($this->resourceConfig, $this->data->setFieldRoles('value', $store));
    }
}
