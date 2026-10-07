<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Model\Export\Catalog;

use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Model\Export\Catalog\ProductAvailability;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \Omikron\Factfinder\Model\Export\Catalog\ProductAvailability
 */
class ProductAvailabilityTest extends TestCase
{
    private MockObject|ModuleManager $moduleManagerMock;
    private MockObject|ObjectManagerInterface $objectManagerMock;
    private MockObject|StockRegistryInterface $stockRegistryMock;
    private MockObject|StoreManagerInterface $storeManagerMock;
    private MockObject|LoggerInterface $loggerMock;
    private ProductAvailability $availability;

    protected function setUp(): void
    {
        $this->moduleManagerMock = $this->createMock(ModuleManager::class);
        $this->objectManagerMock = $this->createMock(ObjectManagerInterface::class);
        $this->stockRegistryMock = $this->createMock(StockRegistryInterface::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->availability = new ProductAvailability(
            $this->moduleManagerMock,
            $this->objectManagerMock,
            $this->storeManagerMock,
            $this->loggerMock
        );
    }

    public function testFallsBackToLegacyInventoryWhenMsiIsDisabled(): void
    {
        $this->moduleManagerMock->method('isEnabled')
            ->with('Magento_InventorySalesApi')
            ->willReturn(false);

        $this->objectManagerMock->method('get')
            ->with(StockRegistryInterface::class)
            ->willReturn($this->stockRegistryMock);

        $productMock = $this->createMock(Product::class);
        $productMock->method('getId')->willReturn(10);

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->method('getWebsiteId')->willReturn(1);
        $productMock->method('getStore')->willReturn($storeMock);

        $stockItemMock = $this->createMock(StockItemInterface::class);
        $stockItemMock->method('getIsInStock')->willReturn(true);

        $this->stockRegistryMock->method('getStockItem')
            ->with(10, 1)
            ->willReturn($stockItemMock);

        $this->assertTrue($this->availability->isAvailable($productMock));
    }
}
