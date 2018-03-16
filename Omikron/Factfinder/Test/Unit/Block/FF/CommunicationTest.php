<?php

namespace Omikron\Factfinder\Test\Unit\Block\FF;

use \Magento\Framework\View\Element\Template\Context;
use \Magento\Framework\Module\Dir\Reader;
use \Magento\Framework\Xml\Parser;
use \Magento\Framework\HTTP\PhpEnvironment\Request;
use Omikron\Factfinder\Block\FF\Communication;
use Omikron\Factfinder\Helper\Tracking;
use Omikron\Factfinder\Helper\Data;

class CommunicationTest extends \PHPUnit_Framework_TestCase
{
    const VERSION = 'version';
    const DEFAULT_QUERY = 'default_query';
    const CHANNEL = 'channel';
    const USE_URL_PARAMETER = true;
    const USE_CACHE = true;
    const ADD_PARAMS = 'add_params';
    const ADD_TRACKING_PARAMS = 'add_tracking_params';
    const KEEP_FILTERS = true;
    const KEEP_URL_PARAMS = true;
    const USE_ASN = true;
    const USE_FOUND_WORDS = true;
    const USE_CAMPAIGNS = true;
    const GENERATE_ADVISOR_TREE = true;
    const DISABLE_CACHE = true;
    const USE_PERSONALIZATION = true;
    const USE_SEMANTIC_ENHANCER = true;
    const USE_ASO = true;
    const USE_BROWSER_HISTORY = true;
    const USE_SEO = true;
    const SEO_PREFIX = 'seo_prefix';
    const SEARCH_IMMEDIATE = 'search_immediate';

    const ADDRESS = 'http://example.com';

    const SESSION_ID = 'session-id';
    const USER_ID = 'user-id';

    /**
     * @var Omikron\Factfinder\Block\FF\Communication
     */
    protected $communication;

    public function setUp()
    {
        $defaultValues['config']['_value']['default']['factfinder']['advanced'] = [
            'version' => self::VERSION,
            'default_query' => self::DEFAULT_QUERY,
            'use_url_parameter' => self::USE_URL_PARAMETER,
            'use_cache' => self::USE_CACHE,
            'add_params' => self::ADD_PARAMS,
            'add_tracking_params' => self::ADD_TRACKING_PARAMS,
            'keep_filters' => self::KEEP_FILTERS,
            'keep_url_params' => self::KEEP_URL_PARAMS,
            'use_asn' => self::USE_ASN,
            'use_found_words' => self::USE_FOUND_WORDS,
            'use_campaigns' => self::USE_CAMPAIGNS,
            'generate_advisor_tree' => self::GENERATE_ADVISOR_TREE,
            'disable_cache' => self::DISABLE_CACHE,
            'use_personalization' => self::USE_PERSONALIZATION,
            'use_semantic_enhancer' => self::USE_SEMANTIC_ENHANCER,
            'use_aso' => self::USE_ASO,
            'use_browser_history' => self::USE_BROWSER_HISTORY,
            'use_seo' => self::USE_SEO,
            'seo_prefix' => self::SEO_PREFIX,
            'search_immediate' => self::SEARCH_IMMEDIATE
        ];
        $defaultValues['config']['_value']['default']['factfinder']['general'] = [
            'channel' => self::CHANNEL
        ];

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->method('getControllerName')
            ->willReturn(Data::CUSTOM_RESULT_PAGE);
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $context->method('getRequest')
            ->willReturn($request);
        $helper = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $helper->method('isEnrichmentEnabled')
            ->willReturn(true);
        $helper->method('getAddress')
            ->willReturn(self::ADDRESS);
        $helper->method('getVersion')
            ->willReturn(self::VERSION);
        $helper->method('getDefaultQuery')
            ->willReturn(self::DEFAULT_QUERY);
        $helper->method('getChannel')
            ->willReturn(self::CHANNEL);
        $helper->method('getUseUrlParameter')
            ->willReturn(self::USE_URL_PARAMETER);
        $helper->method('getUseCache')
            ->willReturn(self::USE_CACHE);
        $helper->method('getAddParams')
            ->willReturn(self::ADD_PARAMS);
        $helper->method('getAddTrackingParams')
            ->willReturn(self::ADD_TRACKING_PARAMS);
        $helper->method('getKeepFilters')
            ->willReturn(self::KEEP_FILTERS);
        $helper->method('getKeepUrlParams')
            ->willReturn(self::KEEP_URL_PARAMS);
        $helper->method('getUseAsn')
            ->willReturn(self::USE_ASN);
        $helper->method('getUseFoundWords')
            ->willReturn(self::USE_FOUND_WORDS);
        $helper->method('getUseCampaigns')
            ->willReturn(self::USE_CAMPAIGNS);
        $helper->method('getGenerateAdvisorTree')
            ->willReturn(self::GENERATE_ADVISOR_TREE);
        $helper->method('getDisableCache')
            ->willReturn(self::DISABLE_CACHE);
        $helper->method('getUsePersonalization')
            ->willReturn(self::USE_PERSONALIZATION);
        $helper->method('getUseSemanticEnhancer')
            ->willReturn(self::USE_SEMANTIC_ENHANCER);
        $helper->method('getUseAso')
            ->willReturn(self::USE_ASO);
        $helper->method('getUseBrowserHistory')
            ->willReturn(self::USE_ASO);
        $helper->method('getUseSeo')
            ->willReturn(self::USE_SEO);
        $helper->method('getSeoPrefix')
            ->willReturn(self::SEO_PREFIX);
        $helper->method('getSearchImmediate')
            ->willReturn(self::SEARCH_IMMEDIATE);

        $moduleDirReader = $this->getMockBuilder(Reader::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parser = $this->getMockBuilder(Parser::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parser->method('load')
            ->withAnyParameters()
            ->willReturn($parser);
        $parser->method('xmlToArray')
            ->withAnyParameters()
            ->willReturn($defaultValues);
        $tracking = $this->getMockBuilder(Tracking::class)
            ->disableOriginalConstructor()
            ->getMock();
        $tracking->method('getSessionId')
            ->willReturn(self::SESSION_ID);
        $tracking->method('getUserId')
            ->willReturn(self::USER_ID);

        $this->communication = new Communication($context, [], $helper, $moduleDirReader, $parser, $tracking);
    }

    public function testGetWebComponent()
    {
        $this->assertNotNull($this->communication->getWebcomponent());
        $this->assertInternalType('string', $this->communication->getWebComponent());
    }
}
