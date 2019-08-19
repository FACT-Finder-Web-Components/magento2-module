<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Catalog\Model\Category;
use Magento\Framework\Event\Observer;
use Magento\Framework\Registry;
use Magento\Framework\View\Layout\ProcessorInterface;
use Magento\Framework\View\LayoutInterface;
use Omikron\Factfinder\Api\Config\FeatureConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CategoryViewTest extends TestCase
{
    /** @var CategoryView */
    private $testee;

    /** @var MockObject|Registry */
    private $registry;

    /** @var MockObject|FeatureConfigInterface */
    private $config;

    public function test_only_activate_for_category_pages()
    {
        $this->withCategory(Category::DM_PRODUCT);
        $this->config->method('useForCategories')->willReturn(true);

        $layout = $this->createMock(LayoutInterface::class);
        $layout->expects($this->never())->method('getUpdate');

        $this->testee->execute($this->withObserver($layout, 'not_a_category'));
    }

    public function test_skip_if_no_category_is_found_in_the_registry()
    {
        $this->config->method('useForCategories')->willReturn(true);

        $layout = $this->createMock(LayoutInterface::class);
        $layout->expects($this->never())->method('getUpdate');

        $this->testee->execute($this->withObserver($layout));
    }

    public function test_skip_if_the_category_only_features_CMS_content()
    {
        $this->withCategory(Category::DM_PAGE);
        $this->config->method('useForCategories')->willReturn(true);

        $layout = $this->createMock(LayoutInterface::class);
        $layout->expects($this->never())->method('getUpdate');

        $this->testee->execute($this->withObserver($layout));
    }

    public function test_skip_if_the_feature_is_deactivated()
    {
        $this->withCategory(Category::DM_PRODUCT);
        $this->config->method('useForCategories')->willReturn(false);

        $layout = $this->createMock(LayoutInterface::class);
        $layout->expects($this->never())->method('getUpdate');

        $this->testee->execute($this->withObserver($layout));
    }

    public function test_handle_is_added_if_all_checks_pass()
    {
        $this->withCategory(Category::DM_PRODUCT);
        $this->config->method('useForCategories')->willReturn(true);

        $update = $this->createMock(ProcessorInterface::class);
        $layout = $this->createConfiguredMock(LayoutInterface::class, ['getUpdate' => $update]);
        $update->expects($this->once())->method('addHandle')->with('factfinder_category_view');

        $this->testee->execute($this->withObserver($layout));
    }

    protected function setUp()
    {
        $this->registry = $this->createMock(Registry::class);
        $this->config   = $this->createMock(FeatureConfigInterface::class);
        $this->testee   = new CategoryView($this->registry, $this->config);
    }

    private function withCategory(string $displayMode): void
    {
        $this->registry->expects($this->any())
            ->method('registry')
            ->with('current_category')
            ->willReturn($this->createConfiguredMock(Category::class, ['getDisplayMode' => $displayMode]));
    }

    private function withObserver(LayoutInterface $layout, string $handle = 'catalog_category_view'): Observer
    {
        return new Observer(['layout' => $layout, 'full_action_name' => $handle]);
    }
}
