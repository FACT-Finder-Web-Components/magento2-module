<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductField;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Phrase;
use Magento\Framework\ObjectManagerInterface;
use Omikron\Factfinder\Api\Export\Catalog\ProductFieldInterface;
use Psr\Log\LoggerInterface;

class GenericField implements ProductFieldInterface
{
    /** @var string */
    private $attributeCode;

    public function __construct(string $attributeCode, ProductAttributeRepositoryInterface $attributeRepository)
    {
        $this->attributeCode = $attributeCode;
        //check if attribute exists
        $attributeRepository->get($this->attributeCode);
    }

    public function getValue(Product $product): string
    {
        return (string) $product->getAttributeText($this->attributeCode);
    }
}
