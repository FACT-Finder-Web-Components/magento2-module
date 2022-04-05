<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Omikron\Factfinder\ViewModel\ProductsPerPage;
use PHPUnit\Framework\TestCase;

class ProductPerPageTest extends TestCase
{
    /** @var ProductsPerPage */
    private $productsPerPage;

    private $scopeConfigMock;

    public function test_will_unserialize_stored_values()
    {
        $this->scopeConfigMock->method('getValue')->with('factfinder/components_options/products_per_page')
            ->willReturn('{"_1648807417584_584":{"value":"8"},"_1648807420544_544":{"value":"12"},"_1648807422801_801":{"value":"16"}}');

        $this->assertIsString($this->productsPerPage->getProductsPerPageConfiguration());
    }

    public function test_output_have_correct_structure()
    {
        $this->scopeConfigMock->method('getValue')->with('factfinder/components_options/products_per_page')
            ->willReturn('{"_1648807417584_584":{"value":"8"},"_1648807420544_544":{"value":"12"},"_1648807422801_801":{"value":"16"}}');

        $actual = $this->productsPerPage->getProductsPerPageConfiguration();

        $this->assertStringContainsString('{"value":"8","selected":false,"default":false}', $actual);
    }

    public function test_return_empty_array_literal_if_no_stored_values()
    {
        $this->scopeConfigMock->method('getValue')->with('factfinder/components_options/products_per_page')
            ->willReturn(null);

        $this->assertEquals('[]', $this->productsPerPage->getProductsPerPageConfiguration());
    }

    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->productsPerPage = new ProductsPerPage(
            $this->scopeConfigMock,
            new Json(),
        );
    }
}
