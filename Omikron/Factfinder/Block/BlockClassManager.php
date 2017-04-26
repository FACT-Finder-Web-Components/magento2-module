<?php

namespace Omikron\Factfinder\Block;

/**
 * Class BlockClassManager
 * Is used in config xml-files to replace block classes on condition of if-config option
 *
 * @package Omikron\Factfinder\Block
 */
class BlockClassManager extends \Magento\Framework\View\Element\Template
{
    /**
     * Replaces the block class with the default searchbox
     *
     * @param $block
     */
    public function setBlockSearchbox($block)
    {
        $this->setBlockClass($block, "top.search", "ff/searchbox.phtml");
    }

    /**
     * Replace the block class and set a default template for a given block
     *
     * @param $block
     * @param $name
     * @param $template
     */
    protected function setBlockClass($block, $name, $template)
    {
        $this->setTemplate($template);
        $this->getLayout()->unsetElement($name);
        $this->getLayout()->createBlock('Omikron\Factfinder\Block\FF\\' . $block, $name);
    }
}