<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Adminhtml\System\Config\Button;

class Feed extends Button
{
    protected function getLabel(): string
    {
        return (string) __('Generate Export File(s) now');
    }

    protected function getTargetUrl(): string
    {
        return $this->getUrl('factfinder/export/feed');
    }
}
