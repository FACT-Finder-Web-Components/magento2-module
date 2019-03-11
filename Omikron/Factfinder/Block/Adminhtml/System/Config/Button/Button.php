<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Adminhtml\System\Config\Button;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Widget\Button as ButtonWidget;

abstract class Button extends Field
{
    public function render(AbstractElement $element): string
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * @param  AbstractElement $_e
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(AbstractElement $_e): string
    {
        return $this->_toHtml();
    }

    public function generateButtonHtml(string $id, string $label): string
    {
        $button = $this->getLayout()->createBlock(ButtonWidget::class)->setData([
                'id'    => $id,
                'label' => __($label)
            ]);

        return $button->toHtml();
    }
}
