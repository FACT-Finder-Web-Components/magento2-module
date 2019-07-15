<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductType;

use Magento\Catalog\Model\Product;
use Omikron\Factfinder\Model\Formatter\NumberFormatter;
use Magento\Bundle\Model\Product\CatalogPrice;
use Magento\Directory\Model\PriceCurrency;

class CompositeDataProvider extends SimpleDataProvider
{
    /** @var CatalogPrice  */
    private $priceModel;

    /** @var PriceCurrency  */
    private $priceCurrency;

    public function __construct(
        PriceCurrency $priceCurrency,
        CatalogPrice $priceModel,
        Product $product,
        NumberFormatter $numberFormatter,
        array $productFields = []
    ){
        parent::__construct($product, $numberFormatter, $productFields);
        $this->priceModel = $priceModel;
        $this->priceCurrency = $priceCurrency;
    }

    public function toArray(): array
    {
        return ['Price' => $this->numberFormatter->format(
                $this->priceCurrency->convert($this->priceModel->getCatalogPrice($this->product)))
            ] + parent::toArray();
    }
}
