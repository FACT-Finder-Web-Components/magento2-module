<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog;

use Omikron\Factfinder\Api\Export\FieldInterface;
use Omikron\Factfinder\Api\Export\FieldProviderInterface;
use Omikron\Factfinder\Model\Config\ExportConfig;
use Omikron\Factfinder\Model\Export\Catalog\ProductField\GenericFieldFactory;

class FieldProvider implements FieldProviderInterface
{
    /** @var ExportConfig */
    private $config;

    /** @var GenericFieldFactory */
    private $fieldFactory;

    /** @var FieldInterface[] */
    private $productFieldProviders;

    /** @var FieldInterface[] */
    private $variantFieldProviders;

    /** @var array */
    private $cachedVariantFields;

    /** @var array */
    private $cachedFields;

    public function __construct
    (
        ExportConfig $config,
        GenericFieldFactory $fieldFactory,
        array $productFields = [],
        array $variantFields = []
    ) {
        $this->config                = $config;
        $this->productFieldProviders = $productFields;
        $this->variantFieldProviders = $variantFields;
        $this->fieldFactory          = $fieldFactory;
    }

    public function getVariantFields(): array
    {
        if (!$this->cachedVariantFields) {
            $this->cachedVariantFields = $this->getFrom($this->variantFieldProviders);
        }
        return $this->cachedVariantFields;
    }

    public function getFields(): array
    {
        if (!$this->cachedFields) {
            $this->cachedFields = $this->getFrom($this->productFieldProviders);
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
