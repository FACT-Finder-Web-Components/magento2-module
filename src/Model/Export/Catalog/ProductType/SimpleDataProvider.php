<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductType;

use Magento\Catalog\Model\Product;
use Omikron\Factfinder\Api\Export\FieldInterface;
use Omikron\Factfinder\Api\Export\DataProviderInterface;
use Omikron\Factfinder\Api\Export\ExportEntityInterface;
use Omikron\Factfinder\Model\Formatter\NumberFormatter;

class SimpleDataProvider implements DataProviderInterface, ExportEntityInterface
{
    /**
     * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
     * phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine
     */
    public function __construct(
        protected Product $product,
        protected NumberFormatter $numberFormatter,
        protected array $productFields = [],
    ) {}

    /**
     * @inheritdoc
     */
    public function getEntities(): iterable
    {
        return [$this];
    }

    public function getId(): int
    {
        return (int) $this->product->getId();
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        $data = [
            'ProductNumber' => (string) $this->product->getSku(),
            'Master'        => (string) $this->product->getData('sku'),
            'Name'          => (string) $this->product->getName(),
            'Description'   => (string) $this->product->getData('description'),
            'Short'         => (string) $this->product->getData('short_description'),
            'Deeplink'      => (string) $this->product->getUrlInStore(),
            'Price'         => $this->numberFormatter->format((float) $this->product->getFinalPrice()),
            'Availability'  => (int) $this->product->isAvailable(),
            'HasVariants'   => 0,
            'MagentoId'     => $this->getId(),
        ];

        return array_reduce(
            $this->productFields,
            fn (array $result, FieldInterface $field): array  => [$field->getName() => $field->getValue($this->product)] + $result,
            $data
        );
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}
