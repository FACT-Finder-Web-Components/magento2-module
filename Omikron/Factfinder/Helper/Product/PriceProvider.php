<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Helper\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Pricing\Price\RegularPrice;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\ConfigurableProduct\Pricing\Price\LowestPriceOptionsProvider;
use Magento\CatalogRule\Model\Rule;

class PriceProvider
{
   /** @var LowestPriceOptionsProvider  */
    protected $lowestPriceOptionsProvider;

   /** @var Rule  */
    protected $ruleModel;

    public function __construct(LowestPriceOptionsProvider $lowestPriceOptionsProvider, Rule $ruleModel)
    {
        $this->lowestPriceOptionsProvider = $lowestPriceOptionsProvider;
        $this->ruleModel                  = $ruleModel;
    }

    /**
     * @param  ProductInterface $product
     * @param  array            $customerGroups
     * @return array
     */
    public function collectPricesForProduct(ProductInterface $product, array $customerGroups) : array
    {
        $prices = [];
        foreach ($customerGroups as $group) {
            if ($product->getTypeId() == Configurable::TYPE_CODE) {
                foreach ($this->lowestPriceOptionsProvider->getProducts($product) as $simple) {
                    $prices [$group] = $this->calculateLowestPriceForProduct($simple, $group);
                }
            } else {
                $prices [$group] = $this->calculateLowestPriceForProduct($product, $group);
            }
        }

        return $prices;
    }

    /**
     * @param ProductInterface $product
     * @param int              $customerGroup
     * @return string
     */
    protected function calculateLowestPriceForProduct(ProductInterface $product, int $customerGroup) : string
    {
        $product->setCustomerGroupId($customerGroup);
        $regularPrice     = $product->getPriceInfo()->getPrice(RegularPrice::PRICE_CODE)->getValue();
        $catalogRulePrice = $this->ruleModel->calcProductPriceRule($product, $regularPrice);

        return $catalogRulePrice > 0.00 ? $this->formatPrice($catalogRulePrice) : $this->formatPrice($regularPrice);
    }

    /**
     * @param float $price
     * @return string
     */
    protected function formatPrice(float $price) : string
    {
        return number_format(round(floatval($price), 2), 2);
    }
}
