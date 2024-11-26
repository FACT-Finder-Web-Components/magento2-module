<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Catalog\Model\Category;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers CategoryPath
 */
class CategoryPathTest extends TestCase
{
    private CategoryPath $categoryPath;
    private Registry $registry;

    /** @var MockObject|Category */
    private MockObject $currentCategory;

    /** @var MockObject|CommunicationConfig */
    private MockObject $communicationConfig;

    /** @var MockObject|ScopeConfigInterface */
    private MockObject $scopeConfig;

    public function test_category_names_are_trimmed()
    {
        $this->communicationConfig->method('getVersion')->willReturn('ng');
        $categoryPath    = $this->newCategoryPath($this->communicationConfig);

        $this->currentCategory->method('getParentCategories')
            ->willReturn([$this->category('Men ', 1), $this->category(' Tops ', 2)]);

        $value = ['Men', 'Tops'];
        $this->assertSame($value, $categoryPath->getCategoryPath());
    }

    protected function setUp(): void
    {
        $this->communicationConfig = $this->createMock(CommunicationConfig::class);
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);
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
        return new CategoryPath($this->registry, $this->communicationConfig, $this->scopeConfig);
    }
}
