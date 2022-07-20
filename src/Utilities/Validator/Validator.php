<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Utilities\Validator;

use Omikron\Factfinder\Exception\ExportPreviewValidationException;

interface Validator
{
    /**
     * @throws ExportPreviewValidationException
     */
    public function validate(): void;
}
