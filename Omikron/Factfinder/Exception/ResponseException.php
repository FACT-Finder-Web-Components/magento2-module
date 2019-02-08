<?php

declare(strict_types = 1);

namespace Omikron\Factfinder\Exception;

use Omikron\Factfinder\Api\Exception\ResponseExceptionInterface;
use Throwable;

class ResponseException extends \DomainException implements ResponseExceptionInterface
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        if (!$message) {
            $message = 'Response body was empty';
        }
        parent::__construct($message, $code, $previous);
    }
}
