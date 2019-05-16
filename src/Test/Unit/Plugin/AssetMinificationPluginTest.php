<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Plugin;

use PHPUnit\Framework\TestCase;

class AssetMinificationPluginTest extends TestCase
{
    /** @var AssetMinificationPlugin */
    private $plugin;

    public function test_js_library_is_added_to_the_exclusion()
    {
        $this->assertContains(AssetMinificationPlugin::JS_LIBRARY, $this->plugin->afterGetExcludes(null, [], 'js'));
        $this->assertNotContains(AssetMinificationPlugin::JS_LIBRARY, $this->plugin->afterGetExcludes(null, [], 'css'));
    }

    protected function setUp()
    {
        $this->plugin = new AssetMinificationPlugin();
    }
}
