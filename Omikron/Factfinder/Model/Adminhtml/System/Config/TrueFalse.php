<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Adminhtml\System\Config;

use Magento\Framework\Data\OptionSourceInterface;

class TrueFalse implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [['value' => 'true', 'label' => __('Yes')], ['value' => 'false', 'label' => __('No')]];
    }
}
