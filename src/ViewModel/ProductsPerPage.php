<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ProductsPerPage implements ArgumentInterface
{
    private const PRODUCT_PER_PAGE_CONFIG_PATH = 'factfinder/components_options/products_per_page';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly Json $serializer
    ) {
    }

    public function getProductsPerPageConfiguration(): string
    {
        $storedValue  = $this->scopeConfig->getValue(self::PRODUCT_PER_PAGE_CONFIG_PATH);
        $unserialized = array_map(function (array $row) {
            return $row + [
                    'selected' => false,
                    'default'  => false
                ];
        }, array_values($this->serializer->unserialize($storedValue ?: '[]')));

        return count($unserialized) ? $this->serializer->serialize($unserialized) : '[]';
    }
}
