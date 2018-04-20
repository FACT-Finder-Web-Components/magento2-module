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
        $this->setValue(md5($this->getValue()));

        parent::beforeSave();
    }
}
