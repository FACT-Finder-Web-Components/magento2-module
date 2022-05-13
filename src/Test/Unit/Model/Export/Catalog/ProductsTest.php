<?php
declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Model\Export\Category;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Model\Export\Catalog\Products;
use Omikron\Factfinder\Test\TestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers Products
 */
class ProductsTest extends TestCase
{
    /** @var MockObject|StoreManagerInterface */
    private MockObject $storeManagerMock;

    /** @var MockObject|ProductRepositoryInterface */
    private MockObject $productRepositoryMock;

    /** @var MockObject|SearchCriteriaBuilder */
    private MockObject $searchCriteriaBuilderMock;

    private Products $products;

    public function test_is_iterable()
    {
        $this->assertTrue(is_iterable($this->products));
    }

    public function test_it_add_all_required_filters()
    {
        $this->searchCriteriaBuilderMock
            ->expects($this->exactly(3))
            ->method('addFilter')->withConsecutive(
                [
                    'status',
                    Status::STATUS_ENABLED
                ],
                [
                    'store_id',
                    Store::DEFAULT_STORE_ID
                ],
                [
                    'visibility',
                    Visibility::VISIBILITY_NOT_VISIBLE,
                    'neq'
                ]
            );

        TestHelper::invokeMethod($this->products, 'getQuery', [1]);
    }

    protected function setUp(): void
    {
        $this->productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);
        $this->storeManagerMock      = $this->createConfiguredMock(StoreManagerInterface::class, [
            'getStore' => $this->createConfiguredMock(
                Store::class,
                ['getId' => Store::DEFAULT_STORE_ID]
            )
        ]);

        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->searchCriteriaBuilderMock->method('addFilter')->willReturn($this->searchCriteriaBuilderMock);
        $this->searchCriteriaBuilderMock->method('setPageSize')->willReturn($this->searchCriteriaBuilderMock);
        $this->searchCriteriaBuilderMock->method('setCurrentPage')->willReturn($this->searchCriteriaBuilderMock);

        $this->products = new Products(
            $this->productRepositoryMock,
            $this->searchCriteriaBuilderMock,
            $this->storeManagerMock,
        );
    }
}
