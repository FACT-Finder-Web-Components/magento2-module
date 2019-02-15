<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Model;

use Omikron\Factfinder\Model\ComponentBuilder;
use PHPUnit\Framework\TestCase;

class ComponentBuilderTest extends TestCase
{
    public function test_build_component_should_generate_correct_xml()
    {
        $attributes = [
            'param1' => 'value1',
            'param2' => 'true',
        ];

        $componentBuilder    = new ComponentBuilder();
        $componentDefinition = $componentBuilder->buildComponent('ff-communication', $attributes);

        $dom = new \DOMDocument();
        $dom->loadXML($componentDefinition);
        $this->assertNotNull($dom->getElementsByTagName('ff-communication')[0]);
        /** @var \DOMElement $ffCommunication */
        $ffCommunication = $dom->getElementsByTagName('ff-communication')[0];
        $this->assertSame('value1', $ffCommunication->getAttribute('param1'));
        $this->assertSame('true', $ffCommunication->getAttribute('param2'));
    }
}
