<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Api;

use Omikron\Factfinder\Exception\ExportPreviewValidationException;

interface ValidatorInterface
{
    /**
     * @throws ExportPreviewValidationException
     */
    public function validate(): void;
}
