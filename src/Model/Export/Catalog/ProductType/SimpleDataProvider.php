<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductType;

use Magento\Catalog\Model\Product;
use Omikron\Factfinder\Api\Export\Catalog\ProductFieldInterface;
use Omikron\Factfinder\Api\Export\DataProviderInterface;
use Omikron\Factfinder\Api\Export\ExportEntityInterface;
use Omikron\Factfinder\Model\Formatter\NumberFormatter;

class SimpleDataProvider implements DataProviderInterface, ExportEntityInterface
{
    /** @var NumberFormatter */
    protected $numberFormatter;

    /** @var Product */
    protected $product;

    /** @var ProductFieldInterface[] */
    private $productFields;

    public function __construct(
        Product $product,
        NumberFormatter $numberFormatter,
        array $productFields = []
    ) {
        $this->product         = $product;
        $this->numberFormatter = $numberFormatter;
        $this->productFields   = $productFields;
    }

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
            'ProductURL'    => (string) $this->product->getUrlInStore(),
            'Price'         => $this->numberFormatter->format((float) $this->product->getFinalPrice()),
            'Brand'         => (string) $this->product->getAttributeText('manufacturer'),
            'Availability'  => (int) $this->product->isAvailable(),
            'HasVariants'   => 0,
            'MagentoId'     => $this->getId(),
        ];

        return array_merge($data, array_map(function (ProductFieldInterface $field): string {
            return $field->getValue($this->product);
        }, $this->productFields));
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}
