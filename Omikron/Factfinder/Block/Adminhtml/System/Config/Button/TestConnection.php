<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Adminhtml\System\Config\Button;

class TestConnection extends Button
{
    /** @var string */
    protected $_template = 'Omikron_Factfinder::system/config/button/test-connection.phtml';

    public function getButtonHtml(): string
    {
        return $this->generateButtonHtml('testconnection_button', 'Test Connection now');
    }
}
