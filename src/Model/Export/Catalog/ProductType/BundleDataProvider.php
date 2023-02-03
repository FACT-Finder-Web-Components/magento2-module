<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductType;

use Magento\Bundle\Model\Product\CatalogPrice;
use Magento\Catalog\Model\Product;
use Magento\Directory\Model\PriceCurrency;
use Omikron\Factfinder\Model\Formatter\NumberFormatter;

class BundleDataProvider extends SimpleDataProvider
{
    public function __construct(
        protected Product         $product,
        protected NumberFormatter $numberFormatter,
        private readonly PriceCurrency     $priceCurrency,
        private readonly CatalogPrice      $priceModel,
        protected array             $productFields = []
    ) {
        parent::__construct($product, $numberFormatter, $productFields);
    }

    public function toArray(): array
    {
        $price = (float) $this->priceCurrency->convert($this->priceModel->getCatalogPrice($this->product));
        return ['Price' => $this->numberFormatter->format($price)] + parent::toArray();
    }
}
