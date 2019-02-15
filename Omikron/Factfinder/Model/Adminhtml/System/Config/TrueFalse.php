<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Adminhtml\System\Config;

use Magento\Framework\App\Config\Value;

class TrueFalse extends Value
{
    public function beforeSave()
    {
        if ((bool) $this->getValue()) {
            $this->setValue('true');
        } else {
            $this->setValue('false');
        }

        return parent::beforeSave();
    }

    public function afterLoad()
    {
        if ($this->getValue() === 'true') {
            $this->setValue('1');
        } else {
            $this->setValue('0');
        }

        return parent::afterLoad();
    }
}
