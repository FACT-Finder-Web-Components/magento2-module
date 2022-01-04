<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\Entity;

use Magento\Catalog\Model\Product;
use Omikron\Factfinder\Api\Export\ExportEntityInterface;
use Omikron\Factfinder\Api\Export\FieldInterface;
use Omikron\Factfinder\Model\Export\Catalog\FieldProvider;
use Omikron\Factfinder\Model\Formatter\NumberFormatter;

class ProductVariation implements ExportEntityInterface
{
    private Product $product;
    private Product $configurable;
    private NumberFormatter $numberFormatter;
    private array $configurableData;
    private FieldProvider $fieldprovider;

    public function __construct(
        Product $product,
        Product $configurable,
        NumberFormatter $numberFormatter,
        FieldProvider $variantFieldProvider,
        array $data = []
    ) {
        $this->product          = $product;
        $this->configurable     = $configurable;
        $this->numberFormatter  = $numberFormatter;
        $this->configurableData = $data;
        $this->fieldprovider    = $variantFieldProvider;
    }

    public function getId(): int
    {
        return (int) $this->product->getId();
    }

    public function toArray(): array
    {
        $baseData = [
                'ProductNumber' => (string) $this->product->getSku(),
                'Price'         => $this->numberFormatter->format((float) $this->product->getFinalPrice()),
                'Availability'  => (int) $this->product->isAvailable(),
                'HasVariants'   => 0,
                'MagentoId'     => $this->getId(),
            ] + $this->configurableData;

        return array_reduce($this->fieldprovider->getVariantFields(),
            function (array $result, FieldInterface $field): array {
                return [$field->getName() => $field->getValue($this->product)] + $result;
            }, $baseData
        );
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getConfigurable(): Product
    {
        return $this->configurable;
    }
}
