<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Adminhtml\System\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class TrueFalseTest extends TestCase
{
    private $trueFalse;

    /**
     * @testdox Value 1 conversion should be 'true' before saving
     */
    public function test_before_save_convert_true_value()
    {
        $value = 1;
        $this->trueFalse->setValue($value);

        $this->trueFalse->beforeSave();

        $this->assertSame('true', $this->trueFalse->getValue());
    }

    /**
     * @testdox Value 0 conversion should be 'false' before saving
     */
    public function test_before_save_convert_false_value()
    {
        $value = 0;
        $this->trueFalse->setValue($value);

        $this->trueFalse->beforeSave();

        $this->assertSame('false', $this->trueFalse->getValue());
    }

    /**
     * @testdox Value 'true' conversion should be 1 after loading
     */
    public function test_after_load_convert_true_value()
    {
        $value = 'true';
        $this->trueFalse->setValue($value);

        $this->trueFalse->afterLoad();

        $this->assertTrue((bool) $this->trueFalse->getValue());
    }

    /**
     * @testdox Value 'false' conversion should be 0 after loading
     */
    public function test_after_load_convert_false_value()
    {
        $value = 'false';
        $this->trueFalse->setValue($value);

        $this->trueFalse->afterLoad();

        $this->assertFalse((bool) $this->trueFalse->getValue());
    }

    protected function setUp()
    {
        $this->trueFalse = (new ObjectManager($this))->getObject(
            TrueFalse::class, [
            'context'            => $this->createMock(Context::class),
            'imageHelperFactory' => $this->createMock(Registry::class),
            'eavConfig'          => $this->createMock(ScopeConfigInterface::class),
            '_eventManager'      => $this->createMock(ManagerInterface::class)
        ]
        );
    }
}
