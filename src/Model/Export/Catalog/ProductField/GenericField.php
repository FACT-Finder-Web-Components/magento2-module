<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductField;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\Export\FieldInterface;
use Omikron\Factfinder\Model\Export\Catalog\AttributeValuesExtractor;

#[AllowDynamicProperties]
class GenericField implements FieldInterface
{
    /**
     * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
     * phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine
     */
    public function __construct(
        private readonly ProductAttributeRepositoryInterface $attributeRepository,
        private readonly AttributeValuesExtractor $valuesExtractor,
        private readonly StoreManagerInterface $storeManager,
        private readonly string $attributeCode,
    ) {
    }

    public function getName(): string
    {
        return (string) $this->getAttribute()->getStoreLabel($this->storeManager->getStore());
    }

    /**
     * @param Product $product
     *
     * @return string
     */
    public function getValue(AbstractModel $product): string
    {
        return implode('|', $this->valuesExtractor->getAttributeValues($product, $this->getAttribute()));
    }

    private function getAttribute(): Attribute
    {
        $this->attribute = $this->attribute ?? $this->attributeRepository->get($this->attributeCode);

        return $this->attribute;
    }
}
