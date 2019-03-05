<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Adminhtml\System\Config\Button;

class CmsFeed extends Feed
{
/** @var string  */
    protected $_template = 'Omikron_Factfinder::system/config/button/cms-feed.phtml';

    public function getButtonHtml(): string
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'cms_feed_button',
                'label' => __('Generate CMS Export File(s) now')
            ]
        );

        return $button->toHtml();
    }
}
