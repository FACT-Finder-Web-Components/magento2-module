<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Catalog\Model\Category;
use Magento\Framework\Registry;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers CategoryPath
 */
class CategoryPathTest extends TestCase
{
    /** @var CategoryPath */
    private $categoryPath;

    /** @var MockObject|Category */
    private $currentCategory;

    /** @var MockObject|CommunicationConfig */
    private $communicationConfig;

    /** @var MockObject|Registry */
    private $registry;

    public function test_category_path_for_ng_version()
    {
        $this->communicationConfig->method('getVersion')->willReturn('ng');
        $categoryPath    = $this->newCategoryPath($this->communicationConfig);

        $this->currentCategory->method('getParentCategories')
            ->willReturn([$this->category('Men 100%', 1), $this->category('Tops & 1/2', 2), $this->category('Jackets +Size ', 3)]);

        $value = 'filter=CategoryPath%3AMen+100%2525%2FTops+%26+1%252F2%2FJackets+%252BSize';
        $this->assertSame($value, (string) $categoryPath->getCategoryPath());
    }

    public function test_category_path_for_standard_version()
    {
        $this->communicationConfig->method('getVersion')->willReturn('7.3');
        $categoryPath    = $this->newCategoryPath($this->communicationConfig);

        $this->currentCategory->method('getParentCategories')
            ->willReturn([$this->category('Jackets +Size', 3), $this->category('Men 100%', 1), $this->category('Tops & 1/2', 2)]);

        $value = 'filterCategoryPathROOT=Men+100%25,filterCategoryPathROOT%2FMen+100%2525=Tops+%26+1%2F2,filterCategoryPathROOT%2FMen+100%2525%2FTops+%26+1%252F2=Jackets+%2BSize';
        $this->assertSame($value, (string) $categoryPath->getAddParams());
    }

    public function test_category_names_are_trimmed()
    {
        $this->communicationConfig->method('getVersion')->willReturn('ng');
        $categoryPath    = $this->newCategoryPath($this->communicationConfig);

        $this->currentCategory->method('getParentCategories')
            ->willReturn([$this->category('Men ', 1), $this->category(' Tops ', 2)]);

        $value = 'filter=CategoryPath%3AMen%2FTops';
        $this->assertSame($value, (string) $categoryPath->getCategoryPath());
    }

    protected function setUp(): void
    {
        $this->communicationConfig = $this->createMock(CommunicationConfig::class);
        $this->currentCategory = $this->createMock(Category::class);
        $this->registry              = new Registry();
        $this->registry->register('current_category', $this->currentCategory);
    }

    private function category(string $name, int $level): Category
    {
        return $this->createConfiguredMock(Category::class, ['getName' => $name, 'getLevel' => $level]);
    }

    private function newCategoryPath(): CategoryPath
    {
        return new CategoryPath($this->registry, $this->communicationConfig);
    }
}
