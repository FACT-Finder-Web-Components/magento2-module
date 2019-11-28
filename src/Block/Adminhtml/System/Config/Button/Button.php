<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Adminhtml\System\Config\Button;

use Magento\Backend\Block\Widget\Button as ButtonWidget;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

abstract class Button extends Field
{
    public function render(AbstractElement $element): string
    {
        $element->unsetData(['scope', 'can_use_website_value', 'can_use_default_value']);
        return parent::render($element);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getElementHtml(AbstractElement $element): string // phpcs:ignore
    {
        /** @var ButtonWidget $button */
        $button = $this->getLayout()->createBlock(ButtonWidget::class);
        $button->setData([
            'label'          => $this->getLabel(),
            'data_attribute' => [
                'mage-init' => ['Omikron_Factfinder/js/ajax-button' => ['url' => $this->getTargetUrl()]],
            ],
        ]);
        return $button->toHtml();
    }

    abstract protected function getLabel(): string;

    abstract protected function getTargetUrl(): string;
}
