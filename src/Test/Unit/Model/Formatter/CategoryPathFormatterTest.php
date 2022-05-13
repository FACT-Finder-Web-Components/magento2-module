<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Model\Formatter;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Store\Model\Store;
use Omikron\Factfinder\Model\Formatter\CategoryPathFormatter;
use PHPUnit\Framework\Constraint\LogicalNot;
use PHPUnit\Framework\Constraint\RegularExpression;
use PHPUnit\Framework\TestCase;

/**
 * @covers CategoryPathFormatterTest
 */
class CategoryPathFormatterTest extends TestCase
{
    /** @var CategoryPathFormatter */
    private $categoryPathFormatter;

    public function test_path_will_be_encoded()
    {
        $storeMock = $this->createConfiguredMock(Store::class, [
            'getId'             => 1,
            'getRootCategoryId' => 2
        ]);

        $path = $this->categoryPathFormatter->format(5, $storeMock);
        $this->assertStringContains(urlencode('Trousers & Pants'), $path);
        $this->assertStringContains(urlencode('5/6 Length Trousers'), $path);
    }

    public function test_invisible_categories_will_be_skipped()
    {
        $storeMock = $this->createConfiguredMock(Store::class, [
            'getId'             => 1,
            'getRootCategoryId' => 2
        ]);

        $path = $this->categoryPathFormatter->format(5, $storeMock);
        $this->assertStringNotContains('Root Catalog', $path);
        $this->assertStringNotContains('Default Category', $path);
    }

    protected function setUp(): void
    {
        $repositoryMock = $this->createMock(CategoryRepositoryInterface::class);
        $repositoryMock->method('get')->willReturnMap(
            [
                [
                    1,
                    1,
                    $this->createConfiguredMock(
                        CategoryInterface::class,
                        [
                            'getName'     => 'Root Catlog',
                            'getPath'     => '1',
                            'getIsActive' => true,
                        ]
                    )
                ],
                [
                    2,
                    1,
                    $this->createConfiguredMock(
                        CategoryInterface::class,
                        [
                            'getName'     => 'Default Category',
                            'getPath'     => '1/2',
                            'getIsActive' => true,
                        ]
                    )
                ],
                [
                    3,
                    1,
                    $this->createConfiguredMock(
                        CategoryInterface::class,
                        [
                            'getName'     => 'Clothes',
                            'getPath'     => '1/2/3',
                            'getIsActive' => true,
                        ]
                    )
                ],
                [
                    4,
                    1,
                    $this->createConfiguredMock(
                        CategoryInterface::class,
                        [
                            'getName'     => 'Trousers & Pants',
                            'getPath'     => '1/2/3/4',
                            'getIsActive' => true,
                        ]
                    )
                ],
                [
                    5,
                    1,
                    $this->createConfiguredMock(
                        CategoryInterface::class,
                        [
                            'getName'     => '5/6 Length Trousers',
                            'getPath'     => '1/2/3/4/5',
                            'getIsActive' => true,
                        ]
                    )
                ],
                [
                    6,
                    1,
                    $this->createConfiguredMock(
                        CategoryInterface::class,
                        [
                            'getName'     => 'Short Trousers',
                            'getPath'     => '1/2/3/4/6',
                            'getIsActive' => true,
                        ]
                    )
                ],
                [
                    7,
                    1,
                    $this->createConfiguredMock(
                        CategoryInterface::class,
                        [
                            'getName'     => 'Not Active category',
                            'getPath'     => '1/2/7',
                            'getIsActive' => false,
                        ]
                    )
                ],
            ]
        );

        $this->categoryPathFormatter = new CategoryPathFormatter($repositoryMock);
    }

    private function assertStringContains(string $pattern, string $string)
    {
        $this->assertThat($string, new RegularExpression('/' . preg_quote($pattern) . '/'), $string);
    }

    private function assertStringNotContains(string $pattern, string $string)
    {
        $this->assertThat($string, new LogicalNot(new RegularExpression('/' . preg_quote($pattern) . '/')));
    }
}
