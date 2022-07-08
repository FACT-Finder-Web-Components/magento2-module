<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Utilities\Validator;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Omikron\Factfinder\Exception\ExportPreviewValidationException;
use Omikron\Factfinder\Utilities\Validator\ExportPreviewValidator;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use PHPUnit\Framework\TestCase;

class ExportPreviewValidatorTest extends TestCase
{
    /** @var ConfigurableType */
    private $configurableType;

    /** @var ProductRepositoryInterface */
    private $repository;

    protected function setUp(): void
    {
        $this->configurableType = $this->createMock(ConfigurableType::class);
        $this->repository = $this->createMock(ProductRepositoryInterface::class);
    }

    public function testShouldThrowExceptionWhenGivenEntityIdIsInvalid()
    {
        // Expect & Given
        $entityIds = [0, 'something', null, '', false];

        foreach ($entityIds as $entityId) {
            $this->expectException(ExportPreviewValidationException::class);
            $this->expectExceptionMessage(sprintf('Product will not be exported. Reason: Product with ID "%s" does not exist.', (int) $entityId));
        }

        // When & Then
        foreach ($entityIds as $entityId) {
            $validator = new ExportPreviewValidator($entityId, $this->repository, $this->configurableType);
            $validator->validate();
        }
    }

    public function testShouldThrowExceptionWhenProductWithGivenEntityIdDoesNotExist()
    {
        // Expect & Given
        $entityId = 94;
        $this->expectException(ExportPreviewValidationException::class);
        $this->expectExceptionMessage(sprintf('Product will not be exported. Reason: Product with ID "%s" does not exist.', $entityId));

        // When
        $validator = new ExportPreviewValidator($entityId, $this->repository, $this->configurableType);

        // Then
        $validator->validate();
    }

    public function testShouldThrowExceptionWhenProductIsNotAvailable()
    {
        // Expect
        $entityId = 2;
        $product = $this->createMock(Product::class);
        $product->method('getName')->willReturn('Bike');
        $product->method('getId')->willReturn(2);
        $product->method('isAvailable')->willReturn(false);
        $repository = $this->createConfiguredMock(ProductRepositoryInterface::class, ['getById' => $product]);
        $this->expectException(ExportPreviewValidationException::class);
        $this->expectExceptionMessage(sprintf('Product will not be exported. Reason: Product "%s" (ID: %s) is not enabled.', $product->getName(), $product->getId()));

        // When
        $validator = new ExportPreviewValidator($entityId, $repository, $this->configurableType);

        // Then
        $validator->validate();
    }
}
