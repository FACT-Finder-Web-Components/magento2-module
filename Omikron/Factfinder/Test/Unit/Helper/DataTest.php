<?php

namespace Omikron\Factfinder\Test\Unit\Helper\Data;

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

    public function setUp()
    {
        $map = [
            [
                'factfinder/general/is_enabled',
                ScopeInterface::SCOPE_STORE,
                null,
                1
            ],
            [
                'factfinder/general/ff_enrichment',
                ScopeInterface::SCOPE_STORE,
                null,
                1
            ],
            [
                'factfinder/general/address',
                ScopeInterface::SCOPE_STORE,
                null,
                'http://example.com'
            ],
            [
                'factfinder/general/channel',
                ScopeInterface::SCOPE_STORE,
                null,
                'magento2-dev'
            ],
            [
                'factfinder/general/username',
                ScopeInterface::SCOPE_STORE,
                null,
                'username'
            ],
            [
                'factfinder/general/password',
                ScopeInterface::SCOPE_STORE,
                null,
                'password'
            ],
            [
                'factfinder/general/show_add_to_card_button',
                ScopeInterface::SCOPE_STORE,
                null,
                1
            ],
            [
                'factfinder/general/authentication_prefix',
                ScopeInterface::SCOPE_STORE,
                null,
                'FF_PREFIX'
            ],
            [
                'factfinder/general/authentication_postfix',
                ScopeInterface::SCOPE_STORE,
                null,
                'FF_POSTFIX'
            ],
            [
                'factfinder/advanced/version',
                ScopeInterface::SCOPE_STORE,
                null,
                '7.3'
            ],
            [
                'factfinder/data_transfer/ff_cron_import',
                ScopeInterface::SCOPE_STORE,
                null,
                1
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
        $resourceConfig = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->data = new Data($context, $resourceConfig);
    }

    public function testIsEnabled()
    {
        $this->assertEquals(true, $this->data->isEnabled());
    }

    public function testisEnrichmentEnabled()
    {
        $this->assertEquals(true, $this->data->isEnrichmentEnabled());
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
}
