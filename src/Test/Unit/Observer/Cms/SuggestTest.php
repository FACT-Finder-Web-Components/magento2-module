<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer\Cms;

use Magento\Framework\Event\Observer;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Model\Config\CmsConfig;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SuggestTest extends TestCase
{
    private const FF_SERVER = 'http://fake-factfinder.com/FACT-Finder-7.3/';

    /** @var Suggest */
    private $observer;

    /** @var MockObject|CmsConfig */
    private $cmsConfig;

    /** @var MockObject|ClientInterface */
    private $client;

    /**
     * @param bool $isEnabled
     * @param bool $useSeparateChannel
     *
     * @testWith [false, false]
     *           [true, false]
     *           [false, true]
     */
    public function test_the_cms_config_is_checked_before_running_a_request(bool $isEnabled, bool $useSeparateChannel)
    {
        $this->cmsConfig->method('isExportEnabled')->willReturn($isEnabled);
        $this->cmsConfig->method('useSeparateChannel')->willReturn($useSeparateChannel);
        $this->client->expects($this->never())->method('sendRequest');
        $this->observer->execute(new Observer());
    }

    public function test_only_add_cms_contents_on_suggest_requests()
    {
        $this->cmsConfig->method('isExportEnabled')->willReturn(true);
        $this->cmsConfig->method('useSeparateChannel')->willReturn(true);
        $this->client->expects($this->never())->method('sendRequest');
        $this->observer->execute(new Observer(['endpoint' => self::FF_SERVER . 'Search.ff']));
    }

    public function test_suggestions_are_loaded_from_cms_channel()
    {
        $this->cmsConfig->method('getChannel')->willReturn('cms_channel');
        $this->cmsConfig->method('isExportEnabled')->willReturn(true);
        $this->cmsConfig->method('useSeparateChannel')->willReturn(true);

        $this->client->expects($this->once())
            ->method('sendRequest')
            ->with(self::FF_SERVER . 'Suggest.ff', ['channel' => 'cms_channel', 'foo' => 'bar'])
            ->willReturn([]);

        $this->observer->execute(new Observer([
            'endpoint' => self::FF_SERVER . 'Suggest.ff',
            'params'   => ['channel' => 'catalog_channel', 'foo' => 'bar'],
        ]));
    }

    protected function setUp()
    {
        $this->cmsConfig = $this->createMock(CmsConfig::class);
        $this->client    = $this->createMock(ClientInterface::class);
        $this->observer  = new Suggest($this->cmsConfig, $this->client);
    }
}
