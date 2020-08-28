<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductField;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Omikron\Factfinder\Api\Export\Catalog\ProductFieldInterface;
use Omikron\Factfinder\Model\Export\Catalog\AttributeValuesExtractor;

class GenericField implements ProductFieldInterface
{
    /** @var ProductAttributeRepositoryInterface */
    private $attributeRepository;

    /** @var AttributeValuesExtractor */
    private $valuesExtractor;

    /** @var string */
    private $attributeCode;

    /** @var Attribute */
    private $attribute;

    public function __construct(
        ProductAttributeRepositoryInterface $attributeRepository,
        AttributeValuesExtractor $valuesExtractor,
        string $attributeCode
    ) {
        $this->valuesExtractor     = $valuesExtractor;
        $this->attributeCode       = $attributeCode;
        $this->attributeRepository = $attributeRepository;
    }

    public function getName(): string
    {
        return (string) $this->getAttribute()->getDefaultFrontendLabel();
    }

    public function getValue(Product $product): string
    {
        return implode('|', $this->valuesExtractor->getAttributeValues($product, $this->getAttribute()));
    }

    private function getAttribute(): Attribute
    {
        $this->attribute = $this->attribute ?? $this->attributeRepository->get($this->attributeCode);
        return $this->attribute;
    }
}
