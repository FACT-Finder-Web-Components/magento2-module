<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute as EavAttribute;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Framework\DataObject;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    /** @var MockObject|AttributeCollectionFactory */
    private $attributeCollectionFactory;

    /** @var Attribute */
    private $sourceModel;

    public function test_items_are_sorted_correctly()
    {
        $this->attributeCollectionFactory->method('create')->willReturn(new DataObject(['items' => [
            $this->createAttribute('third', 'Third'),
            $this->createAttribute('first', 'First'),
            $this->createAttribute('second', 'Second'),
        ]]));

        $expected = [
            ['value' => '', 'label' => ''],
            ['value' => 'first', 'label' => 'First'],
            ['value' => 'second', 'label' => 'Second'],
            ['value' => 'third', 'label' => 'Third'],
        ];
        $this->assertSame($expected, $this->sourceModel->toOptionArray());
    }

    public function test_labels_and_values_are_cast_to_string()
    {
        $this->attributeCollectionFactory->method('create')->willReturn(new DataObject(['items' => [
            $this->createAttribute(null, null),
            $this->createAttribute(null, null),
        ]]));

        $expected = [
            ['value' => '', 'label' => ''],
            ['value' => '', 'label' => ''],
            ['value' => '', 'label' => ''],
        ];
        $this->assertSame($expected, $this->sourceModel->toOptionArray());
    }

    protected function setUp()
    {
        $this->attributeCollectionFactory = $this->getMockBuilder(AttributeCollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->sourceModel = new Attribute($this->attributeCollectionFactory);
    }

    private function createAttribute(?string $value, ?string $label)
    {
        return $this->createConfiguredMock(EavAttribute::class, [
            'getAttributeCode'        => $value,
            'getDefaultFrontendLabel' => $label,
        ]);
    }
}
