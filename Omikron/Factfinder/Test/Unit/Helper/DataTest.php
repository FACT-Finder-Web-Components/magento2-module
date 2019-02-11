<?php

namespace Omikron\Factfinder\Helper;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Omikron\Factfinder\Helper\Data as DataHelper;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    /** @var DataHelper */
    private $helper;

    /**
     * @testdox The session ID should be 30 characters long
     */
    public function testCorrectSessionId()
    {
        $this->assertSame('shortershortershortershortersh', $this->helper->getCorrectSessionId('shorter'));
        $this->assertSame('cc4df8ae0af123bb4dc2f7f91c6a8b', $this->helper->getCorrectSessionId(sha1('longer')));
        $this->assertSame('7ddf32e17a6ac5ce04a8ecbf782ca5', $this->helper->getCorrectSessionId(''));
    }

    protected function setUp()
    {
        $this->helper = (new ObjectManager($this))->getObject(DataHelper::class);
    }
}

function uniqid(): string
{
    return 'random';
}
