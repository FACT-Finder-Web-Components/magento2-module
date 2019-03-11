<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Adminhtml\System\Config\Button;

class Feed extends Button
{
    /** @var string */
    protected $_template = 'Omikron_Factfinder::system/config/button/feed.phtml';

    public function getButtonHtml(): string
    {
        return $this->generateButtonHtml('feed_button','Generate Export File(s) now');
    }
}
