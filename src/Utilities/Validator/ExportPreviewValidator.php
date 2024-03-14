<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Utilities\Validator;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Omikron\Factfinder\Api\ValidatorInterface;
use Omikron\Factfinder\Exception\ExportPreviewValidationException;

class ExportPreviewValidator implements ValidatorInterface
{
    /**
     * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
     * phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine
     */
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ConfigurableType           $configurableType,
        private readonly int                        $entityId,
    ) {
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
            $message = sprintf('Product "%s" (ID: %s) is not enabled.', $product->getName(), $product->getId());
            throw new ExportPreviewValidationException($message);
        }

        /** phpcs:disable PSR2.ControlStructures.ControlStructureSpacing.SpacingAfterOpenBrace */
        if (
            $this->isVariant($product) === false
            && (int) $product->getVisibility() === Visibility::VISIBILITY_NOT_VISIBLE
        ) {
            $message = sprintf(
                'Product "%s" (ID: %s) has "Visibility" set to "Not Visible Individually".',
                $product->getName(),
                $product->getId()
            );
            throw new ExportPreviewValidationException($message);
        }
    }

    private function isVariant(Product $product): bool
    {
        return count($this->configurableType->getParentIdsByChild($product->getId())) > 0;
    }
}
