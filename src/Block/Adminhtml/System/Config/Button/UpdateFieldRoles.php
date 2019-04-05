<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Adminhtml\System\Config\Button;

class UpdateFieldRoles extends Button
{
    protected function getLabel(): string
    {
        return (string) __('Update Field Roles');
    }

    protected function getTargetUrl(): string
    {
        return $this->getUrl('factfinder/fieldroles/update');
    }
}
