<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Adminhtml\System\Config\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\DataObject;
use Omikron\Factfinder\Model\Config\Source\Attribute as AttributeSource;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class ExportFields extends AbstractFieldArray
{
    /** @var Select */
    private $attributeRenderer;

    /** @var Select */
    private $typeRenderer;

    /** @var AttributeSource */
    private $attributeSource;

    /** @var Yesno */
    private $boolSource;

    public function __construct(
        Context $context,
        AttributeSource $attributeSource,
        Yesno $boolSource,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->attributeSource = $attributeSource;
        $this->boolSource      = $boolSource;
    }

    protected function _prepareToRender()
    {
        $this->_addAfter = false;
        $this->addColumn('code', [
            'label'    => __('Attribute'),
            'class'    => 'required-entry',
            'renderer' => $this->getAttributeRenderer(),
        ]);
        $this->addColumn('multi', [
            'label'    => __('Multi-Attribute'),
            'class'    => 'required-entry',
            'renderer' => $this->getMultiRenderer(),
        ]);
    }

    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $type = $row->getData('code');
        if ($type !== null) {
            $options['option_' . $this->getAttributeRenderer()->calcOptionHash($type)] = 'selected';
        }

        $multi = $row->getData('multi');
        if ($multi !== null) {
            $options['option_' . $this->getMultiRenderer()->calcOptionHash($multi)] = 'selected';
        }

        $row->setData('option_extra_attrs', $options);
    }

    private function getAttributeRenderer(): Select
    {
        $this->attributeRenderer = $this->attributeRenderer ?? $this->createSelect($this->attributeSource);
        return $this->attributeRenderer;
    }

    private function getMultiRenderer(): Select
    {
        $this->typeRenderer = $this->typeRenderer ?? $this->createSelect($this->boolSource);
        $this->typeRenderer->setData('value', 0);
        return $this->typeRenderer;
    }

    private function createSelect(OptionSourceInterface $optionSource): Select
    {
        $block = $this->getLayout()->createBlock(Select::class, '', ['data' => ['is_render_to_js_template' => true]]);
        return $block->setOptions($optionSource->toOptionArray());
    }
}
