<?php

namespace Omikron\Factfinder\Model\Source;

/**
 * Class Attribute
 * @package Omikron\Factfinder\Model\Source
 */
class Attribute
{
    /* @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection */
    protected $_attributeCollection;

    /**
     * Attribute constructor.
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $attributeCollection
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $attributeCollection
    )
    {
        $this->_attributeCollection = $attributeCollection;
    }

    /**
     * Generate an array of option attributes
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => '',
                'label' => ''
            ]
        ];

        $attributeCollection = $this->_attributeCollection->load()->getItems();

        foreach ($attributeCollection as $attribute) {
            $options[] = [
                'value' => $attribute->getAttributeCode(),
                'label' => $attribute->getAttributeCode()
            ];
        }

        asort($options);

        return $options;
    }
}
