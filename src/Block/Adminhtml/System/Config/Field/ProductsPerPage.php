<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Adminhtml\System\Config\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\DataObject;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class ProductsPerPage extends AbstractFieldArray
{

    protected function _prepareToRender()
    {
        $this->_addAfter = false;
        $this->addColumn('value', [
            'label'    => __('Num of records'),
            'class'    => 'required-entry',
        ]);
    }
}
