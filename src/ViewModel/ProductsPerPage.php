<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ProductsPerPage implements ArgumentInterface
{
    private const PRODUCT_PER_PAGE_CONFIG_PATH = 'factfinder/components_options/products_per_page';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(ScopeConfigInterface $scopeConfig, Json $json)
    {
        $this->scopeConfig = $scopeConfig;
        $this->serializer  = $json;
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
