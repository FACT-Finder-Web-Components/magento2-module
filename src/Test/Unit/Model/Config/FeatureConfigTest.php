<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FeatureConfigTest extends TestCase
{
    /** @var FeatureConfig */
    private $testee;

    /** @var ScopeConfigInterface|MockObject */
    private $scopeConfig;

    /** @var CommunicationConfig|MockObject */
    private $communicationConfig;

    /**
     * @testdox Categories should be rendered with FACT-Finder only if the integration AND the feature are active
     * @testWith [false, false, false]
     *           [true, false, false]
     *           [false, true, false]
     *           [true, true, true]
     */
    public function test_use_for_categories(bool $generalConfig, bool $featureConfig, bool $expected)
    {
        $this->communicationConfig->method('isChannelEnabled')->willReturn($generalConfig);
        $this->scopeConfig->method('isSetFlag')
            ->with('factfinder/general/use_for_categories', ScopeInterface::SCOPE_STORES)
            ->willReturn($featureConfig);

        $this->assertSame($expected, $this->testee->useForCategories());
    }

    protected function setUp(): void
    {
        $this->scopeConfig         = $this->createMock(ScopeConfigInterface::class);
        $this->communicationConfig = $this->createMock(CommunicationConfig::class);

        $this->testee = new FeatureConfig($this->scopeConfig, $this->communicationConfig);
    }
}
