<?php
declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Model\Export\Category;

use Magento\Catalog\Api\CategoryListInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Model\Export\Category\Categories;
use Omikron\Factfinder\Test\TestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers CategoriesTest
 */
class CategoriesTest extends TestCase
{
    private const DEFAULT_CATEGORY_ID = 2;

    /** @var MockObject|StoreManagerInterface */
    private $storeManagerMock;

    /** @var MockObject|CategoryListInterface */
    private $categoryListMock;

    /** @var MockObject|SearchCriteriaBuilder */
    private $searchCriteriaBuilderMock;

    /** @var Categories */
    private $categories;

    public function test_is_iterable()
    {
        $this->assertTrue(is_iterable($this->categories));
    }

    public function test_it_filter_out_tree_root_and_default_category()
    {
        $this->searchCriteriaBuilderMock
            ->expects($this->once())
            ->method('addFilter')->with(
                'entity_id',
                [
                    Category::TREE_ROOT_ID,
                    self::DEFAULT_CATEGORY_ID
                ]
            );

        TestHelper::invokeMethod($this->categories, 'getCriteria');
    }

    protected function setUp(): void
    {
        $this->categoryListMock = $this->createMock(CategoryListInterface::class);
        $this->storeManagerMock = $this->createConfiguredMock(StoreManagerInterface::class, [
            'getStore' => $this->createConfiguredMock(
                Store::class,
                ['getRootCategoryId' => self::DEFAULT_CATEGORY_ID]
            )
        ]);

        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->searchCriteriaBuilderMock->method('addFilter')->willReturn($this->searchCriteriaBuilderMock);

        $this->categories = new Categories(
            $this->searchCriteriaBuilderMock,
            $this->storeManagerMock,
            $this->categoryListMock
        );
    }
}
