<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Adminhtml\System\Config\Button;

class CmsFeed extends Button
{
    /** @var string */
    protected $_template = 'Omikron_Factfinder::system/config/button/cms-feed.phtml';

    public function getButtonHtml(): string
    {
        return $this->generateButtonHtml('cms_feed_button','Generate CMS feed now');
    }
}
