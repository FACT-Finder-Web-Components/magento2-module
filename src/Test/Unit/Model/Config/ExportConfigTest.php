<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface as Scope;
use PHPUnit\Framework\TestCase;

/**
 * @covers ExportConfig
 */
class ExportConfigTest extends TestCase
{
    /** @var ExportConfig */
    private $testee;

    public function test_multiattributes_are_calculated_correctly()
    {
        $result = $this->testee->getMultiAttributes(42);
        $this->assertContains('climate', $result);
        $this->assertNotContains('color', $result);
        $this->assertNotContains('gender', $result);
        $this->assertNotContains('size', $result);
    }

    public function test_multiattributes_are_calculated_correctly_for_numerical_attribute()
    {
        $result = $this->testee->getMultiAttributes(42, true);
        $this->assertContains('size', $result);
        $this->assertNotContains('color', $result);
        $this->assertNotContains('gender', $result);
        $this->assertNotContains('climate', $result);
    }

    public function test_single_fields_are_calculated_correctly()
    {
        $result = $this->testee->getSingleFields(42);
        $this->assertNotContains('climate', $result);
        $this->assertContains('color', $result);
        $this->assertContains('gender', $result);
        $this->assertCount(2, $result);
    }

    public function test_correct_push_data_types_are_returned_for_different_ff_versions()
    {
        $result = $this->testee->getPushImportDataTypes(1);
        $this->assertContains('data', $result);
        $this->assertNotContains('search', $result);

        $result = $this->testee->getPushImportDataTypes(1);
        $this->assertContains('search', $result);
        $this->assertNotContains('data', $result);
    }

    protected function setUp(): void
    {
        $scopeConfig  = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturnMap([
            ['factfinder/data_transfer/ff_push_import_type', Scope::SCOPE_STORES, 1, 'search,suggest'],
            ['factfinder/export/attributes', Scope::SCOPE_STORES, 42,'{"_1":{"code":"color","multi":"0","numerical":"0"},"_2":{"code":"climate","multi":"1","numerical":"0"},"_3":{"code":"gender","multi":"0","numerical":"0"},"_4":{"code":"size","multi":"1","numerical":"1"}}']
        ]);
        $communicationConfig = $this->createMock(CommunicationConfig::class);
        $communicationConfig->method('getVersion')->willReturnOnConsecutiveCalls('7.3', 'ng');
        $this->testee = new ExportConfig($scopeConfig, new Json(), $communicationConfig);
    }
}
