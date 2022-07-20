<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Exception;

class ExportPreviewValidationException extends \Exception
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Product will not be exported. Reason: %s', $message), $code, $previous);
    }
}
