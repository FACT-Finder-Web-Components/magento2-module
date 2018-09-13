<?php

namespace Omikron\Factfinder\Model\Authentication;

use \Magento\Framework\App\Config\Value;

class Password extends Value
{
    /**
     * It makes password encrypted by md5
     *
     * @return $this|void
     */
    public function beforeSave()
    {
        $value = md5($this->getValue());

        if (preg_match('/^[a-f0-9]{32}$/i', $this->getValue())) {
            $value = $this->getOldValue();
        }

        $this->setValue($value);

        parent::beforeSave();
    }

    /**
     * Get & decrypt old value from configuration
     *
     * @return string
     */
    public function getOldValue()
    {
        return parent::getOldValue();
    }
}
