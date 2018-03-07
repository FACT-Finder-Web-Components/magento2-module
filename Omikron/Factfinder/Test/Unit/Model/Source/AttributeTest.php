<?php

namespace Omikron\Factfinder\Test\Unit\Model\Source;

use Omikron\Factfinder\Model\Source\Attribute;
use \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection;
use \Magento\Framework\Data\Collection as MagentoCollection;
use \Magento\Eav\Model\Entity\Attribute as EntityAttribute;

class AttributeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Data\Collection
     */
    protected $collection;

    public function setUp()
    {
        $this->collection = $this->getMockBuilder(MagentoCollection::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testToOptionArray()
    {
        $attributeCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $attributeA = $this->getMockBuilder(EntityAttribute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $attributeA->method('getAttributeCode')
            ->willReturn('codeA');
        $attributeB = $this->getMockBuilder(EntityAttribute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $attributeB->method('getAttributeCode')
            ->willReturn('codeB');
        $data = array(
            $attributeB,
            $attributeA
        );
        $this->collection->method('getItems')
            ->willReturn($data);
        $attributeCollection->method('load')
            ->willReturn($this->collection);
        $attribute = new Attribute($attributeCollection);

        $this->assertNotNull($attribute->toOptionArray());
        $this->assertInternalType('array', $attribute->toOptionArray());
        $this->assertTrue(count($attribute->toOptionArray()) === 3);

        $expectedArray[0] = ['value' => '', 'label' => ''];
        $expectedArray[1] = ['value' => 'codeB', 'label' => 'codeB'];
        $expectedArray[2] = ['value' => 'codeA', 'label' => 'codeA'];

        $this->assertEquals($expectedArray, $attribute->toOptionArray());
    }

    public function testToOptionArrayWithAttributesEmptyArray()
    {
        $attributeCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $data = [];
        $this->collection->method('getItems')
            ->willReturn($data);
        $attributeCollection->method('load')
            ->willReturn($this->collection);
        $attribute = new Attribute($attributeCollection);

        $this->assertNotNull($attribute->toOptionArray());
        $this->assertInternalType('array', $attribute->toOptionArray());
        $this->assertTrue(count($attribute->toOptionArray()) === 1);

        $expectedArray[0] = ['value' => '', 'label' => ''];

        $this->assertEquals($expectedArray, $attribute->toOptionArray());
    }
}
