<?php

namespace Omikron\Factfinder\Test\Unit\Model\Source;

use Omikron\Factfinder\Model\Source\FFVersion;

class FFVersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Omikron\Factfinder\Model\Source\FFVersion
     */
    protected $ffVersion;

    public function setUp()
    {
        $this->ffVersion = new FFVersion();
    }

    public function testToOptionArray()
    {
        $optionsArray = [
            ['value' => '7.2', 'label' => __('7.2')],
            ['value' => '7.3', 'label' => __('7.3')]
        ];

        $this->assertEquals($optionsArray, $this->ffVersion->toOptionArray());
    }

    public function testToArray()
    {
        $optionsInKeyValueFormat = [
            '7.2' => __('7.2'),
            '7.3' => __('7.3')
        ];

        $this->assertEquals($optionsInKeyValueFormat, $this->ffVersion->toArray());
    }
}