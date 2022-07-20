<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Utilities\Validator;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Omikron\Factfinder\Exception\ExportPreviewValidationException;

class ExportPreviewValidator implements Validator
{
    private ProductRepositoryInterface $productRepository;
    private ConfigurableType $configurableType;
    private int $entityId;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ConfigurableType $configurableType,
        int $entityId
    ) {
        $this->productRepository = $productRepository;
        $this->configurableType  = $configurableType;
        $this->entityId          = $entityId;
    }

    public function validate(): void
    {
        if ($this->entityId === 0) {
            throw new ExportPreviewValidationException(sprintf('Product with ID "%s" does not exist.', $this->entityId));
        }

        /** @var Product $product */
        $product = $this->productRepository->getById($this->entityId);

        if ($product === null) {
            throw new ExportPreviewValidationException(sprintf('Product with ID "%s" does not exist.', $this->entityId));
        }

        if ($product->isAvailable() === false) {
            throw new ExportPreviewValidationException(sprintf('Product "%s" (ID: %s) is not enabled.', $product->getName(), $product->getId()));
        }

        if (
            $this->isVariant($product) === false
            && (int) $product->getVisibility() === Visibility::VISIBILITY_NOT_VISIBLE
        ) {
            throw new ExportPreviewValidationException(sprintf('Product "%s" (ID: %s) has "Visibility" set to "Not Visible Individually".', $product->getName(), $product->getId()));
        }
    }

    private function isVariant(Product $product): bool
    {
        return count($this->configurableType->getParentIdsByChild($product->getId())) > 0;
    }
}
