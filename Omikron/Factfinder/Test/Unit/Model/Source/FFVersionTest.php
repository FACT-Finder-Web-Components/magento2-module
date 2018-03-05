<?php

namespace Omikron\Factfinder\Test\Unit\Model;

use Omikron\Factfinder\Model\Source\FFVersion;

class FFVersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FFVersion
     */
    protected $ffVersion;

    public function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->ffVersion = $objectManager->getObject('Omikron\Factfinder\Model\Source\FFVersion');
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