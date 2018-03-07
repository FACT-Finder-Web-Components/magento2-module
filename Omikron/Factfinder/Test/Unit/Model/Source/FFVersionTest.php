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
        $this->assertNotNull($this->ffVersion->toOptionArray());
        $this->assertInternalType('array', $this->ffVersion->toOptionArray());
    }

    public function testToArray()
    {
        $this->assertNotNull($this->ffVersion->toArray());
        $this->assertInternalType('array', $this->ffVersion->toArray());
    }
}
