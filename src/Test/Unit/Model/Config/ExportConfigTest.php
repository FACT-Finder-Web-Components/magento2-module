<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use PHPUnit\Framework\TestCase;

class ExportConfigTest extends TestCase
{
    /** @var ExportConfig */
    private $testee;

    public function test_multiattributes_are_calculated_correctly()
    {
        $this->assertArraySubset(['climate'], $this->testee->getMultiAttributes(42));
    }

    public function test_single_fields_are_calculated_correctly()
    {
        $this->assertArraySubset(['color', 'gender'], $this->testee->getSingleFields(42));
    }

    protected function setUp()
    {
        $scopeConfig  = $this->createMock(ScopeConfigInterface::class);
        $this->testee = new ExportConfig($scopeConfig, new Json());

        $scopeConfig->method('getValue')
            ->with('factfinder/export/attributes', 'stores', 42)
            ->willReturn('{"_1":{"code":"color","multi":"0"},"_2":{"code":"climate","multi":"1"},"_3":{"code":"gender","multi":"0"}}');
    }
}
