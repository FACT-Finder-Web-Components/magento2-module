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
            ->willReturn([$this->category('Tops', 2), $this->category('Jackets', 3), $this->category('Men', 1)]);

        $value = 'filter=CategoryPath%3AMen%2FTops%2FJackets';
        $this->assertSame($value, (string) $categoryPath->getCategoryPath());
    }

    public function test_category_path_for_standard_version()
    {
        $this->communicationConfig->method('getVersion')->willReturn('7.3');
        $categoryPath    = $this->newCategoryPath($this->communicationConfig);

        $this->currentCategory->method('getParentCategories')
            ->willReturn([$this->category('Jackets', 3), $this->category('Men', 1), $this->category('Tops', 2)]);

        $value = 'filterCategoryPathROOT=Men,filterCategoryPathROOT%2FMen=Tops,filterCategoryPathROOT%2FMen%2FTops=Jackets';
        $this->assertSame($value, (string) $categoryPath->getAddParams());
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

    private function newCategoryPath(CommunicationConfig $communicationConfig): CategoryPath
    {
        return new CategoryPath($this->registry, $this->communicationConfig);;
    }
}
