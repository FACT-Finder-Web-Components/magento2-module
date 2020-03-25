<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Adminhtml\System\Config\Button;

class TestFtpConnection extends Button
{
    protected function getLabel(): string
    {
        return (string) __('Test FTP Connection now');
    }

    protected function getTargetUrl(): string
    {
        return $this->getUrl('factfinder/testconnection/testftpconnection');
    }
}
