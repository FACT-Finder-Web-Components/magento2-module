<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Exception;

class ResponseException extends \RuntimeException
{
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null) // phpcs:ignore
    {
        parent::__construct($message ?: 'Response body was empty', $code, $previous);
    }
}
