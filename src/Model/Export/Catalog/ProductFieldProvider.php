<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog;

use Omikron\Factfinder\Api\Export\Catalog\ProductFieldInterface;
use Omikron\Factfinder\Model\Config\ExportConfig;
use Omikron\Factfinder\Model\Export\Catalog\ProductField\GenericFieldFactory;

class ProductFieldProvider
{
    /** @var ExportConfig */
    private $config;

    /** @var ProductFieldInterface[] */
    private $productFields;

    /** @var GenericFieldFactory */
    private $fieldFactory;

    public function __construct(ExportConfig $config, GenericFieldFactory $fieldFactory, array $productFields)
    {
        $this->config        = $config;
        $this->productFields = $productFields;
        $this->fieldFactory  = $fieldFactory;
    }

    public function getFields(): array
    {
        return array_reduce($this->config->getSingleFields(), function (array $fields, string $attributeCode): array {
            $attribute = $this->fieldFactory->create(['attributeCode' => $attributeCode]);
            return [$attribute->getName() => $attribute] + $fields;
        }, $this->productFields);
    }
}
