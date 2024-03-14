<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog;

use Omikron\Factfinder\Api\Export\FieldProviderInterface;
use Omikron\Factfinder\Model\Config\ExportConfig;
use Omikron\Factfinder\Model\Export\Catalog\ProductField\GenericFieldFactory;

class FieldProvider implements FieldProviderInterface
{
    private ?array $cachedFields;
    private ?array $cachedVariantFields;

    public function __construct(
        private readonly ExportConfig $config,
        private readonly GenericFieldFactory $fieldFactory,
        private readonly array $productFields = [],
        private readonly array $variantFields = [],
    ) {
    }

    public function getVariantFields(): array
    {
        if (!isset($this->cachedVariantFields)) {
            $this->cachedVariantFields = $this->getFrom($this->variantFields);
        }
        return $this->cachedVariantFields;
    }

    public function getFields(): array
    {
        if (!isset($this->cachedFields)) {
            $this->cachedFields = $this->getFrom($this->productFields);
        }
        return $this->cachedFields;
    }

    public function getFrom(array $fields): array
    {
        return array_reduce($this->config->getSingleFields(), function (array $fields, string $attributeCode): array {
            $attribute = $this->fieldFactory->create(['attributeCode' => $attributeCode]);
            return [$attribute->getName() => $attribute] + $fields;
        }, $fields);
    }
}
