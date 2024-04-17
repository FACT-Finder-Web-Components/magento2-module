<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\Entity;

use Magento\Catalog\Model\Product;
use Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku;
use Omikron\Factfinder\Api\Export\ExportEntityInterface;
use Omikron\Factfinder\Api\Export\FieldInterface;
use Omikron\Factfinder\Model\Export\Catalog\FieldProvider;
use Omikron\Factfinder\Model\Formatter\NumberFormatter;

class ProductVariation implements ExportEntityInterface
{
    private Product $product;
    private Product $configurable;
    private NumberFormatter $numberFormatter;
    private FieldProvider $fieldprovider;

    /** @var string[] */
    private array $configurableData;
    private GetSourceItemsBySku $getSourceItemsBySku;

    public function __construct(
        Product $product,
        Product $configurable,
        NumberFormatter $numberFormatter,
        FieldProvider $variantFieldProvider,
        GetSourceItemsBySku $getSourceItemsBySku,
        array $data = []
    ) {
        $this->product          = $product;
        $this->configurable     = $configurable;
        $this->numberFormatter  = $numberFormatter;
        $this->configurableData = $data;
        $this->fieldprovider    = $variantFieldProvider;
        $this->getSourceItemsBySku = $getSourceItemsBySku;
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
                'Availability'  => (int) $this->getAvailability(),
                'HasVariants'   => 0,
                'MagentoId'     => $this->getId(),
            ] + $this->configurableData;

        list($filterAttributes, $restFields) = $this->extractFilterAttributes($this->fieldprovider->getVariantFields());
        $parentAttributes        = $this->configurableData['FilterAttributes'] ?? '';
        $variantAttributes       = $filterAttributes ? $filterAttributes->getValue($this->product) : '';
        $splicedFilterAttributes = str_replace('||', '|', $parentAttributes . $variantAttributes);

        return ($splicedFilterAttributes ? ['FilterAttributes' => $splicedFilterAttributes] : [])
            + array_reduce(
                $restFields,
                fn (array $result, FieldInterface $field): array => [$field->getName() => $field->getValue($this->product)] + $result,
                $baseData
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

    private function extractFilterAttributes(array $fields): array
    {
        $withoutFilterAttributes = array_filter($fields, fn (FieldInterface $field): bool => $field->getName() !== 'FilterAttributes');

        $filterAttributes = $fields['FilterAttributes'] ?? [];

        return [$filterAttributes, $withoutFilterAttributes];
    }

    private function getAvailability(): bool
    {
        if ($this->product->hasData('is_salable')) {
            return $this->product->isAvailable();
        }

        $sourceItems = $this->getSourceItemsBySku->execute($this->product->getSku());

        foreach ($sourceItems as $sourceItem) {
            if ($sourceItem->getStatus()) {
                return true;
            }
        }

        return false;
    }
}
