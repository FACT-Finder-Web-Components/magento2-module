<?php
// @codingStandardsIgnoreFile

namespace Omikron\Factfinder\Test\Unit\Helper\Product;

use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Pricing\Price\LowestPriceOptionsProvider;
use Magento\CatalogRule\Model\Rule;
use Magento\Framework\Pricing\PriceInfo\Base;
use Magento\Framework\Pricing\Price\PriceInterface;
use Omikron\Factfinder\Helper\Product\PriceProvider;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class PriceProviderTest extends \PHPUnit\Framework\TestCase
{
    /** @var LowestPriceOptionsProvider | \PHPUnit_Framework_MockObject_MockObject */
    protected $lowestPriceOptionsProviderMock;

    /** @var Rule | \PHPUnit_Framework_MockObject_MockObject */
    protected $ruleModelMock;

    /** @var Product | \PHPUnit_Framework_MockObject_MockObject */
    protected $productMock;

    /** @var PriceProvider */
    protected $priceProvider;

    public function testCalculatePriceFromConfigurableProductNoCatalogRules()
    {
        $lowestPriceProduct = [$this->createProductMock('simple', 20)];
        $this->lowestPriceOptionsProviderMock->method('getProducts')->willReturn($lowestPriceProduct);
        $configurableProductMock =  $this->createProductMock('configurable', 0);

        $prices = $this->priceProvider->collectPricesForProduct($configurableProductMock, [1, 2, 3]);

        $this->assertSame([1 => '20.00', 2 => '20.00', 3 => '20.00'], $prices, 'Prices should be equal to 20 as the lower price option ');
    }

    public function testCalculatePriceFromConfigurableProductCatalogRulesApplied()
    {
        $productMocks [] = $this->createProductMock('simple', 20);
        $this->ruleModelMock->method('calcProductPriceRule')->willReturnOnConsecutiveCalls(15.00, 12.00, 12.00);
        $this->lowestPriceOptionsProviderMock->method('getProducts')->willReturn($productMocks);
        $productMocks = $this->createProductMock('configurable', 0);
        $prices = $this->priceProvider->collectPricesForProduct($productMocks, [1, 2, 3]);

        $this->assertSame([1 => '15.00', 2 => '12.00', 3 => '12.00'], $prices, 'Returned product prices should be equal to calculated product price rule');
    }

    public function testGetPriceFromSimpleProductNoCatalogRules ()
    {
        $this->lowestPriceOptionsProviderMock->expects($this->never())->method('getProducts');
        $productMock = $this->createProductMock('simple', 55);
        $prices = $this->priceProvider->collectPricesForProduct($productMock, [1, 2, 3]);
        $this->ruleModelMock->expects($this->never())->method('calcProductPriceRule');

        $this->assertSame([1 => '55.00', 2 => '55.00', 3 => '55.00'], $prices, 'Returned product prices should be equal to calculated product price rule');
    }

    public function testGetPriceFromSimpleProductCatalogRulesApplied()
    {
        $this->lowestPriceOptionsProviderMock->expects($this->never())->method('getProducts');
        $this->ruleModelMock->method('calcProductPriceRule')->willReturnOnConsecutiveCalls(45.00, 45.00, 40.00);
        $productMock = $this->createProductMock('simple', 55);
        $prices = $this->priceProvider->collectPricesForProduct($productMock, [1, 2, 3]);

        $this->assertSame([1 => '45.00', 2 => '45.00', 3 => '40.00'], $prices);
    }

    public function testCorrectNumberOfPricesAreFetched()
    {
        $productMocks [] = $this->createProductMock('simple', 20);
        $this->ruleModelMock->method('calcProductPriceRule')->willReturnOnConsecutiveCalls(1, 2, 3, 4, 5, 6);
        $this->lowestPriceOptionsProviderMock->method('getProducts')->willReturn($productMocks);
        $this->ruleModelMock->expects($this->exactly(3))->method('calcProductPriceRule');
        $productMock = $this->createProductMock('simple', 0);
        $prices = $this->priceProvider->collectPricesForProduct($productMock, [1, 2, 3]);

        $this->assertEquals(3, count($prices));
    }

    public function testRegularPriceIsTakenWhenCatalogRulePriceIsZero()
    {
        $this->lowestPriceOptionsProviderMock->expects($this->never())->method('getProducts');
        $this->ruleModelMock->method('calcProductPriceRule')->willReturnOnConsecutiveCalls(0.00, 0.00, 20.00);
        $productMock = $this->createProductMock('simple', 35);
        $prices = $this->priceProvider->collectPricesForProduct($productMock, [1, 2, 3]);

        $this->assertSame([1 => '35.00', 2 => '35.00', 3 => '20.00'], $prices, '0.00 prices should not be taken into account');
    }

    /**
     * @param string $typeId
     * @param float  $price
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createProductMock($typeId, $price)
    {
        $productMock   = $this->createMock(Product::class);
        $priceInfoMock = $this->createMock(Base::class);
        $priceMock     = $this->createMock(PriceInterface::class);
        $priceMock->method('getValue')->willReturn($price);
        $productMock->method('getTypeId')->willReturn($typeId);
        $priceInfoMock->method('getPrice')->willReturn($priceMock);
        $productMock->method('getPriceInfo')->willReturn($priceInfoMock);

        return $productMock;
    }

    protected function setUp()
    {
        $this->lowestPriceOptionsProviderMock = $this->createMock(LowestPriceOptionsProvider::class);
        $this->ruleModelMock                  = $this->createMock(Rule::class);
        $this->priceProvider                  = (new ObjectManager($this))
            ->getObject(
                \Omikron\Factfinder\Helper\Product\PriceProvider::class,
                [
                    'lowestPriceOptionsProvider' => $this->lowestPriceOptionsProviderMock,
                    'ruleModel'                  => $this->ruleModelMock,
                ]
            );
    }
}
