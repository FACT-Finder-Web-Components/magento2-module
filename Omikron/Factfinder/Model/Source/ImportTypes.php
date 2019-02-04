<?php

namespace Omikron\Factfinder\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ImportTypes implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'data', 'label' => __('Data')],
            ['value' => 'suggest', 'label' => __('Suggest')],
        ];
    }
}
