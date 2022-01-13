<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductField;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\Export\FieldInterface;
use Omikron\Factfinder\Model\Export\Catalog\AttributeValuesExtractor;
use Magento\Catalog\Model\Product;

class GenericField implements FieldInterface
{
    private ProductAttributeRepositoryInterface $attributeRepository;
    private AttributeValuesExtractor $valuesExtractor;
    private string $attributeCode;
    private Attribute $attribute;
    private StoreManagerInterface $storeManager;

    public function __construct(
        ProductAttributeRepositoryInterface $attributeRepository,
        AttributeValuesExtractor $valuesExtractor,
        StoreManagerInterface $storeManager,
        string $attributeCode
    ) {
        $this->valuesExtractor     = $valuesExtractor;
        $this->attributeCode       = $attributeCode;
        $this->attributeRepository = $attributeRepository;
        $this->storeManager        = $storeManager;
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
