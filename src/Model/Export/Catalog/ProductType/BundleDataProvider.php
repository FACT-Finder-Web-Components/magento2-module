<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductType;

use Magento\Bundle\Model\Product\CatalogPrice;
use Magento\Catalog\Model\Product;
use Magento\Directory\Model\PriceCurrency;
use Omikron\Factfinder\Model\Formatter\NumberFormatter;

class BundleDataProvider extends SimpleDataProvider
{
    private CatalogPrice $priceModel;
    private PriceCurrency $priceCurrency;

    public function __construct(
        Product $product,
        NumberFormatter $numberFormatter,
        PriceCurrency $priceCurrency,
        CatalogPrice $priceModel,
        array $productFields = []
    ) {
        parent::__construct($product, $numberFormatter, $productFields);
        $this->priceModel    = $priceModel;
        $this->priceCurrency = $priceCurrency;
    }

    public function toArray(): array
    {
        $price = (float) $this->priceCurrency->convert($this->priceModel->getCatalogPrice($this->product));
        return ['Price' => $this->numberFormatter->format($price)] + parent::toArray();
    }
}
