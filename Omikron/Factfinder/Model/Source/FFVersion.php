<?php

namespace Omikron\Factfinder\Model\Source;

/**
 * Class FFVersion
 *
 * @package Omikron\Factfinder\Model\Source
 */
class FFVersion implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '7.2', 'label' => __('7.2')],
            ['value' => '7.3', 'label' => __('7.3')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            '7.2' => __('7.2'),
            '7.3' => __('7.3'),
        ];
    }
}
