<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductField;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\Store;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CategoryPathTest extends TestCase
{
    /** @var CategoryPath */
    private $categoryPath;

    /** @var MockObject|CategoryRepositoryInterface */
    private $repositoryMock;

    /** @var MockObject|AbstractModel */
    private $productMock;

    public function test_path_will_be_encoded()
    {
        $this->productMock->method('getCategoryIds')->willReturn(['5']);
        $path = $this->categoryPath->getValue($this->productMock);
        $this->assertStringContains(urlencode('Trousers & Pants'), $path);
        $this->assertStringContains(urlencode('5/6 Length Trousers'), $path);
    }

    public function test_multiple_category_branches_will_be_exported()
    {
        $this->productMock->method('getCategoryIds')->willReturn(['5', '6']);
        $path = $this->categoryPath->getValue($this->productMock);
        $this->assertEquals($path, 'Clothes/Trousers+%26+Pants/5%2F6+Length+Trousers|Clothes/Trousers+%26+Pants/Short+Trousers');
    }

    public function test_invisible_categories_will_be_skipped()
    {
        $this->productMock->method('getCategoryIds')->willReturn(['5']);
        $path = $this->categoryPath->getValue($this->productMock);
        $this->assertStringNotContains('Root Catalog', $path);
        $this->assertStringNotContains('Default Category', $path);
    }


    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(CategoryRepositoryInterface::class);
        $this->repositoryMock->method('get')->willReturnMap(
            [
                [
                    1, 1, $this->createConfiguredMock(
                    CategoryInterface::class,
                    [
                        'getName'     => 'Root Catlog',
                        'getPath'     => '1',
                        'getIsActive' => true,
                    ])
                ],
                [
                    2, 1, $this->createConfiguredMock(
                    CategoryInterface::class,
                    [
                        'getName'     => 'Default Category',
                        'getPath'     => '1/2',
                        'getIsActive' => true,
                    ])
                ],
                [
                    3, 1, $this->createConfiguredMock(
                    CategoryInterface::class,
                    [
                        'getName'     => 'Clothes',
                        'getPath'     => '1/2/3',
                        'getIsActive' => true,
                    ])
                ],
                [
                    4, 1, $this->createConfiguredMock(
                    CategoryInterface::class,
                    [
                        'getName'     => 'Trousers & Pants',
                        'getPath'     => '1/2/3/4',
                        'getIsActive' => true,
                    ])
                ],
                [
                    5, 1, $this->createConfiguredMock(
                    CategoryInterface::class,
                    [
                        'getName'     => '5/6 Length Trousers',
                        'getPath'     => '1/2/3/4/5',
                        'getIsActive' => true,
                    ])
                ],
                [
                    6, 1, $this->createConfiguredMock(
                    CategoryInterface::class,
                    [
                        'getName'     => 'Short Trousers',
                        'getPath'     => '1/2/3/4/6',
                        'getIsActive' => true,
                    ])
                ],
                [
                    7, 1, $this->createConfiguredMock(
                    CategoryInterface::class,
                    [
                        'getName'     => 'Not Active category',
                        'getPath'     => '1/2/7',
                        'getIsActive' => false,
                    ])
                ],
            ]);
        $this->productMock = $this->getMockBuilder(AbstractModel::class)
            ->disableOriginalConstructor()
            ->setMethods(['getStore', 'getCategoryIds'])
            ->getMock();

        $this->productMock->method('getStore')
            ->willReturn($this->createConfiguredMock(Store::class, [
                'getId'             => 1,
                'getRootCategoryId' => 2
            ]));

        $this->categoryPath = new CategoryPath($this->repositoryMock, 'CategoryPath');
    }

    private function assertStringContains(string $needle, string $haystack)
    {

        $this->assertMatchesRegularExpression('/' . preg_quote($needle) . '/', $haystack);
    }

    public function assertStringNotContains($needle, $haystack)
    {
        $this->assertDoesNotMatchRegularExpression('/' . preg_quote($needle) . '/', $haystack);
    }
}
