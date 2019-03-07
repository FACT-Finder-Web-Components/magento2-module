<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Adminhtml\System\Config\Button;

use Magento\Backend\Block\Widget\Button;

class CmsFeed extends Feed
{
    /** @var string */
    protected $_template = 'Omikron_Factfinder::system/config/button/cms-feed.phtml';

    public function getButtonHtml(): string
    {
        /** @var Button $button */
        $button = $this->getLayout()->createBlock(Button::class);
        $button->setData([
            'id'    => 'cms_feed_button',
            'label' => __('Generate CMS feed now'),
        ]);
        return $button->toHtml();
    }
}
