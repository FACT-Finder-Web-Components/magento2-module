<?php

namespace Omikron\Factfinder\Model\Source;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute as EavAttribute;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Attribute implements OptionSourceInterface
{
    /** @var AttributeCollectionFactory */
    private $collectionFactory;

    public function __construct(AttributeCollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        $collection = $this->collectionFactory->create();
        $collection->setOrder('attribute_code', 'ASC');
        $options = array_map(function (EavAttribute $attribute): array {
            return ['value' => $attribute->getAttributeCode(), 'label' => $attribute->getAttributeCode()];
        }, $collection->getItems());
        return array_merge([['value' => '', 'label' => '']], $options);
    }
}
