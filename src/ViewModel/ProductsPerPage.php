<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ProductsPerPage implements ArgumentInterface
{
    private const PRODUCT_PER_PAGE_CONFIG_PATH = 'factfinder/components_options/products_per_page';
    private const DEFAULT_PRODUCT_PER_PAGE_CONFIG = '10, 15, 20, 30, 50';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly Json $serializer
    ) {
    }

    public function getProductsPerPageConfiguration(): string
    {
        $storedValue  = $this->scopeConfig->getValue(self::PRODUCT_PER_PAGE_CONFIG_PATH);
        $unserialized = array_values($this->serializer->unserialize($storedValue ?: '[]'));
        $pppConfig = array_column($unserialized, 'value');

        return count($pppConfig) ? implode(', ', $pppConfig) : self::DEFAULT_PRODUCT_PER_PAGE_CONFIG;
    }
}
