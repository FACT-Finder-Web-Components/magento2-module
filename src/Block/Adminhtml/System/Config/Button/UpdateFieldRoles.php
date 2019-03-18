<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Adminhtml\System\Config\Button;

class UpdateFieldRoles extends Button
{
    /** @var string */
    protected $_template = 'Omikron_Factfinder::system/config/button/update-field-roles.phtml';

    public function getButtonHtml(): string
    {
        return $this->generateButtonHtml('updatefieldroles_button', 'Update Field Roles');
    }
}
