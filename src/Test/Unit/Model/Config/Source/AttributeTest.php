<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Adminhtml\System\Config\Source;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute as EavAttribute;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Framework\DataObject;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers Attribute
 */
class AttributeTest extends TestCase
{
    private Attribute $sourceModel;

    /** @var MockObject|AttributeCollectionFactory */
    private MockObject $attributeCollectionFactory;

    public function test_items_are_sorted_correctly()
    {
        $this->attributeCollectionFactory->method('create')->willReturn(new DataObject(['items' => [
            $this->createAttribute('third', 'Third'),
            $this->createAttribute('first', 'First'),
            $this->createAttribute('second', 'Second'),
        ]]));

        $expected = [
            ['value' => 'first', 'label' => 'First'],
            ['value' => 'second', 'label' => 'Second'],
            ['value' => 'third', 'label' => 'Third'],
        ];
        $this->assertSame($expected, $this->sourceModel->toOptionArray());
    }

    public function test_empty_values_are_omitted()
    {
        $this->attributeCollectionFactory->method('create')->willReturn(new DataObject(['items' => [
            $this->createAttribute('second', 'Second'),
            $this->createAttribute(null, null),
            $this->createAttribute('first', 'First'),
        ]]));

        $expected = [
            ['value' => 'first', 'label' => 'First'],
            ['value' => 'second', 'label' => 'Second'],
        ];
        $this->assertSame($expected, $this->sourceModel->toOptionArray());
    }

    protected function setUp(): void
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
