<?php

namespace Omikron\Factfinder\Api\Export\Catalog;

use Magento\Catalog\Model\Product;

/**
 * @api
 */
interface ProductFieldInterface
{
    public function getName(): string;

    public function getValue(Product $product): string;
}
