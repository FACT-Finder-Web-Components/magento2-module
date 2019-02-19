<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Catalog\Helper\Image;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ProductBasedComponent implements ArgumentInterface
{
    private const PATH_SHOW_ADD_TO_CART_BUTTON = 'factfinder/general/show_add_to_card_button';

    /** @var Image */
    protected $imageHelper;

    /** @var ScopeConfigInterface  */
    protected $scopeConfig;

    private $urlBuilder;

    public function __construct(Image $imageHelper, ScopeConfigInterface $scopeConfig, UrlInterface $urlBuilder)
    {
        $this->imageHelper = $imageHelper;
        $this->scopeConfig = $scopeConfig;
        $this->urlBuilder  = $urlBuilder;
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
}
