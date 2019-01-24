<?php

namespace Omikron\Factfinder\Model\Source;

/**
 * Class ImportTypes
 */
class ImportTypes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'data', 'label' => __('Data')],
            ['value' => 'suggest', 'label' => __('Suggest')]
        ];
    }
}