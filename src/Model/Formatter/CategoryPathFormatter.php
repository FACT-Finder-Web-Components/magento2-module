<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Formatter;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;

class CategoryPathFormatter
{
    public function __construct(private readonly CategoryRepositoryInterface $categoryRepository)
    {
    }

    public function format(int $categoryId, Store $store): string
    {
        return implode('/', array_map('urlencode', $this->getPath($categoryId, $store)));
    }

    /**
     * @param int   $categoryId
     * @param Store $store
     *
     * @return string[]
     */
    private function getPath(int $categoryId, Store $store): array
    {
        try {
            $storeId = (int) $store->getId();
            return array_map(function (int $id) use ($storeId): string {
                return trim($this->getCategory($id, $storeId)->getName());
            }, $this->getPathIds($this->getCategory($categoryId, $storeId), $store));
        } catch (NoSuchEntityException $e) {
            return [];
        }
    }

    private function getPathIds(CategoryInterface $category, Store $store): array
    {
        $path = explode('/', (string) $category->getPath());
        $root = (int) ($path[1] ?? -1);
        return $category->getIsActive() && $store->getRootCategoryId() == $root ? array_slice($path, 2) : [];
    }

    /**
     * @param int $categoryId
     * @param int $storeId
     *
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    private function getCategory(int $categoryId, int $storeId): CategoryInterface
    {
        return $this->categoryRepository->get($categoryId, $storeId);
    }
}
