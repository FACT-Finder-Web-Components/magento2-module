<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\Entity;

use Magento\Catalog\Model\Product;
use Omikron\Factfinder\Api\Export\Catalog\ProductFieldInterface;
use Omikron\Factfinder\Api\Export\ExportEntityInterface;
use Omikron\Factfinder\Model\Formatter\NumberFormatter;

class ProductVariation implements ExportEntityInterface
{
    /** @var Product */
    private $product;

    /** @var Product */
    private $configurable;

    /** @var NumberFormatter */
    private $numberFormatter;

    /** @var array */
    private $configurableData;

    /** @var array */
    private $fields;

    public function __construct(
        Product $product,
        Product $configurable,
        NumberFormatter $numberFormatter,
        array $data = [],
        array $fields = []
    ) {
        $this->product          = $product;
        $this->configurable     = $configurable;
        $this->numberFormatter  = $numberFormatter;
        $this->configurableData = $data;
        $this->fields           = $fields;
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

        return array_merge($baseData, array_map(function (ProductFieldInterface $field): string {
            return $field->getValue($this->product);
        }, $this->fields));
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
