<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config\Source;

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
        $options = array_map(function (EavAttribute $attribute): array {
            return ['value' => $attribute->getAttributeCode(), 'label' => $attribute->getDefaultFrontendLabel()];
        }, $this->collectionFactory->create()->getItems());

        usort($options, function (array $a, array $b): int {
            return strtolower($a['label']) <=> strtolower($b['label']);
        });

        return array_merge([['value' => '', 'label' => '']], $options);
    }
}
