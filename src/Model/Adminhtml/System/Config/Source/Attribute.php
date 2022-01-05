<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Adminhtml\System\Config\Source;

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
        $options = array_map(fn(EavAttribute $attribute) => ['value' => (string) $attribute->getAttributeCode(), 'label' => (string) $attribute->getDefaultFrontendLabel()], $this->collectionFactory->create()->getItems());

        $options = array_filter($options, fn (array $a) => (bool)!!$a['label']);

        usort($options, fn(array $a, array $b) => (int)strtolower($a['label']) <=> strtolower($b['label']));

        return $options;
    }
}
