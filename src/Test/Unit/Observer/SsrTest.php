<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\Layout\ProcessorInterface;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers Ssr
 */
class SsrTest extends TestCase
{
    private Ssr $observer;

    /** @var MockObject|ScopeConfigInterface */
    private MockObject $scopeConfigMock;

    public function test_add_handle_to_layout()
    {
        $this->featureActive(true);
        $update = $this->createMock(ProcessorInterface::class);
        $update->expects($this->atLeastOnce())->method('addHandle');

        $this->withLayoutUpdate($update, ['handle_with_ssr']);
    }

    public function test_do_nothing_if_the_feature_is_disabled()
    {
        $this->featureActive(false);
        $update = $this->createMock(ProcessorInterface::class);
        $update->expects($this->never())->method('addHandle');

        $this->withLayoutUpdate($update, []);
    }

    public function test_no_handle_is_added_when_factfinder_is_not_used()
    {
        $this->featureActive(true);
        $update = $this->createMock(ProcessorInterface::class);
        $update->expects($this->never())->method('addHandle');

        $this->withLayoutUpdate($update, ['handle_without_ssr']);
    }

    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->observer        = new Ssr($this->scopeConfigMock, ['handle_with_ssr']);
    }

    private function featureActive(bool $active): void
    {
        $this->scopeConfigMock->method('isSetFlag')
            ->with('factfinder/general/use_ssr', $this->stringStartsWith('store'), $this->anything())
            ->willReturn($active);
    }

    private function withLayoutUpdate(ProcessorInterface $update, array $handles = []): void
    {
        $update->method('getHandles')->willReturn($handles);
        $layout = $this->createConfiguredMock(LayoutInterface::class, ['getUpdate' => $update]);
        $this->observer->execute($this->createConfiguredMock(Observer::class, ['getData' => $layout]));
    }
}
