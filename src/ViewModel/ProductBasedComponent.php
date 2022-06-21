<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ProductBasedComponent implements ArgumentInterface
{
    private const PATH_SHOW_ADD_TO_CART_BUTTON = 'factfinder/general/show_add_to_cart_button';
    private const PATH_MAX_RESULT = 'factfinder/components_options/max_results_';

    /** @var Image */
    private $imageHelper;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var UrlInterface */
    private $urlBuilder;

    /** @var Registry */
    private $registry;

    public function __construct(
        Image $imageHelper,
        ScopeConfigInterface $scopeConfig,
        UrlInterface $urlBuilder,
        Registry $registry
    ) {
        $this->imageHelper = $imageHelper;
        $this->scopeConfig = $scopeConfig;
        $this->urlBuilder  = $urlBuilder;
        $this->registry    = $registry;
    }

    public function getProduct(): ProductInterface
    {
        return $this->registry->registry('product');
    }

    public function getProductImagePlaceholder(): string
    {
        return (string) $this->imageHelper->getDefaultPlaceholderUrl('image');
    }

    public function getAddToCartUrl(): string
    {
        return (string) $this->urlBuilder->getUrl('checkout/cart/add');
    }

    public function isAddToCartEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::PATH_SHOW_ADD_TO_CART_BUTTON);
    }

    public function getMaxResult(string $component = 'recommendation'): int
    {
        $storedValue      = (int) $this->scopeConfig->getValue(self::PATH_MAX_RESULT . $component);
        $defaultMaxResult = 4;

        return $storedValue > 0 ? $storedValue : $defaultMaxResult;
    }
}
