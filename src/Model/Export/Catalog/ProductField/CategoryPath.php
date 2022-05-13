<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductField;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Model\AbstractModel;
use Omikron\Factfinder\Api\Export\FieldInterface;
use Omikron\Factfinder\Model\Formatter\CategoryPathFormatter;

class CategoryPath implements FieldInterface
{
    /** @var CategoryRepositoryInterface */
    private $categoryRepository;

    /** @var CategoryPathFormatter */
    private $categoryPathFormatter;

    /** @var string */
    private $fieldName;

    public function __construct(CategoryRepositoryInterface $categoryRepository, string $fieldName = 'CategoryPath')
    {
        $this->categoryRepository    = $categoryRepository;
        $this->fieldName             = $fieldName;
        $this->categoryPathFormatter = new CategoryPathFormatter($this->categoryRepository);
    }

    public function getName(): string
    {
        return $this->fieldName;
    }

    /**
     * @param Product $product
     *
     * @return string
     */
    public function getValue(AbstractModel $product): string
    {
        $paths = array_map(function (int $categoryId) use ($product): string {
            return $this->categoryPathFormatter->format($categoryId, $product->getStore());
        }, $product->getCategoryIds());

        return implode('|', array_filter($paths));
    }
}
