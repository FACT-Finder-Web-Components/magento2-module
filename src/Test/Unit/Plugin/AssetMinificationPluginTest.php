<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Plugin;

use PHPUnit\Framework\TestCase;

/**
 * @covers AssetMinificationPlugin
 */
class AssetMinificationPluginTest extends TestCase
{
    /** @var AssetMinificationPlugin */
    private $plugin;

    public function test_js_library_is_added_to_the_exclusion()
    {
        $this->assertContains(AssetMinificationPlugin::JS_LIBRARY, $this->plugin->afterGetExcludes(null, [], 'js'));
        $this->assertNotContains(AssetMinificationPlugin::JS_LIBRARY, $this->plugin->afterGetExcludes(null, [], 'css'));
    }

    protected function setUp(): void
    {
        $this->plugin = new AssetMinificationPlugin();
    }
}
