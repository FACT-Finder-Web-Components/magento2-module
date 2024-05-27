<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Adminhtml\System\Config\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class ParameterWhitelist extends AbstractFieldArray
{
    protected function _prepareToRender()
    {
        $this->_addAfter = false;
        $this->addColumn('name', [
            'label'    => __('Parameter name'),
            'class'    => 'required-entry',
        ]);
    }
}
