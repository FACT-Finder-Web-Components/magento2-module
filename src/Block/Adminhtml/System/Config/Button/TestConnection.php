<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Adminhtml\System\Config\Button;

class TestConnection extends Button
{
    protected function getLabel(): string
    {
        return (string) __('Test Connection');
    }

    protected function getTargetUrl(): string
    {
        return $this->getUrl('factfinder/testconnection/testconnection');
    }
}
