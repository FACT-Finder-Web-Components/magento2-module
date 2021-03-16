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
    private $productFields;

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
