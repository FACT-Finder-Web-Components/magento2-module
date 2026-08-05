<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog;

use Magento\Catalog\Model\Product;
use Magento\InventorySalesApi\Api\AreProductsSalableInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Resolves the availability of a product the same way the storefront does.
 *
 * The MSI source items are not a reliable source of truth. In single source mode the salability is
 * derived from the legacy stock tables, so an installation which writes stock data without going
 * through the MSI synchronization ends up with products which are salable everywhere but have no
 * source item at all.
 */
class ProductAvailability
{
    /** @var array<string, int> */
    private array $stockIds = [];

    public function __construct(
        private readonly AreProductsSalableInterface $areProductsSalable,
        private readonly StockResolverInterface $stockResolver,
        private readonly StoreManagerInterface $storeManager,
    ) {
    }

    public function isAvailable(Product $product): bool
    {
        if ($product->hasData('is_salable')) {
            return $product->isAvailable();
        }

        foreach ($this->areProductsSalable->execute([(string) $product->getSku()], $this->getStockId()) as $result) {
            if ($result->isSalable()) {
                return true;
            }
        }

        return false;
    }

    private function getStockId(): int
    {
        $websiteCode = (string) $this->storeManager->getWebsite()->getCode();

        return $this->stockIds[$websiteCode] ??= (int) $this->stockResolver
            ->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode)
            ->getStockId();
    }
}
