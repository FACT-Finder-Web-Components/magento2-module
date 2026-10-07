<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog;

use Magento\Catalog\Model\Product;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Handles product availability check with support for both MSI and Legacy CatalogInventory.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductAvailability
{
    private const MSI_MODULE_NAME = 'Magento_InventorySalesApi';
    private const ARE_PRODUCTS_SALABLE_INTERFACE = 'Magento\InventorySalesApi\Api\AreProductsSalableInterface';
    private const STOCK_RESOLVER_INTERFACE = 'Magento\InventorySalesApi\Api\StockResolverInterface';
    private const SALES_CHANNEL_INTERFACE = 'Magento\InventorySalesApi\Api\Data\SalesChannelInterface';
    private const LEGACY_STOCK_REGISTRY_INTERFACE = 'Magento\CatalogInventory\Api\StockRegistryInterface';

    private ?bool $isMsiAvailable = null;
    private mixed $areProductsSalable = null;
    private mixed $stockResolver = null;
    private mixed $legacyStockRegistry = null;

    public function __construct(
        private readonly ModuleManager $moduleManager,
        private readonly ObjectManagerInterface $objectManager,
        private readonly StoreManagerInterface $storeManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function isAvailable(Product $product): bool
    {
        if ($this->canUseMsi()) {
            return $this->isAvailableViaMsi($product);
        }

        return $this->isAvailableViaLegacyInventory($product);
    }

    private function canUseMsi(): bool
    {
        if ($this->isMsiAvailable === null) {
            $this->isMsiAvailable = $this->moduleManager->isEnabled(self::MSI_MODULE_NAME)
                && interface_exists(self::ARE_PRODUCTS_SALABLE_INTERFACE)
                && interface_exists(self::STOCK_RESOLVER_INTERFACE);
        }

        return $this->isMsiAvailable;
    }

    private function isAvailableViaMsi(Product $product): bool
    {
        try {
            $areProductsSalable = $this->getAreProductsSalable();
            $stockResolver = $this->getStockResolver();

            if (!$areProductsSalable || !$stockResolver) {
                return $this->isAvailableViaLegacyInventory($product);
            }

            $websiteCode = $this->storeManager->getWebsite()->getCode();
            $typeWebsite = defined(self::SALES_CHANNEL_INTERFACE . '::TYPE_WEBSITE')
                ? constant(self::SALES_CHANNEL_INTERFACE . '::TYPE_WEBSITE')
                : 'website';

            $stock = $stockResolver->execute($typeWebsite, $websiteCode);
            $stockId = (int) $stock->getId();

            $sku = (string) $product->getSku();
            $salableList = $areProductsSalable->execute([$sku], $stockId);

            $result = $salableList[$sku] ?? reset($salableList);

            return $result ? (bool) $result->isSalable() : false;
        } catch (\Throwable $e) {
            $this->logger->warning(sprintf(
                '[FactFinder] Failed to determine MSI availability for SKU "%s", falling back to legacy: %s',
                $product->getSku(),
                $e->getMessage()
            ));

            return $this->isAvailableViaLegacyInventory($product);
        }
    }

    /**
     * Resolves availability via classic Magento CatalogInventory (Non-MSI).
     * We use ObjectManager here to avoid Hard Dependencies on deprecated core classes.
     */
    private function isAvailableViaLegacyInventory(Product $product): bool
    {
        try {
            if (!interface_exists(self::LEGACY_STOCK_REGISTRY_INTERFACE)) {
                return false;
            }

            if ($this->legacyStockRegistry === null) {
                $this->legacyStockRegistry = $this->objectManager->get(self::LEGACY_STOCK_REGISTRY_INTERFACE);
            }

            $websiteId = (int) $product->getStore()->getWebsiteId();
            $stockItem = $this->legacyStockRegistry->getStockItem((int) $product->getId(), $websiteId);

            return (bool) $stockItem->getIsInStock();
        } catch (\Throwable $e) {
            $this->logger->warning(sprintf(
                '[FactFinder] Failed to determine legacy stock for product ID "%d": %s',
                $product->getId(),
                $e->getMessage()
            ));

            return false;
        }
    }

    private function getAreProductsSalable(): mixed
    {
        if ($this->areProductsSalable === null) {
            $this->areProductsSalable = $this->objectManager->get(self::ARE_PRODUCTS_SALABLE_INTERFACE);
        }

        return $this->areProductsSalable;
    }

    private function getStockResolver(): mixed
    {
        if ($this->stockResolver === null) {
            $this->stockResolver = $this->objectManager->get(self::STOCK_RESOLVER_INTERFACE);
        }

        return $this->stockResolver;
    }
}
