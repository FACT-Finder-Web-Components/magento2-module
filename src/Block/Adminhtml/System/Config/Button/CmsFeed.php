<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Adminhtml\System\Config\Button;

class CmsFeed extends Button
{
    protected function getLabel(): string
    {
        return (string) __('Generate CMS feed now');
    }

    protected function getTargetUrl(): string
    {
        return $this->getUrl('factfinder/export/cmsfeed');
    }
}
