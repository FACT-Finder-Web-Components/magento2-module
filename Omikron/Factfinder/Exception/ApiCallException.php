<?php

declare(strict_types = 1);

namespace Omikron\Factfinder\Exception;

use Omikron\Factfinder\Api\Exception\ApiCallExceptionInterface;
use Omikron\Factfinder\Api\Exception\ResponseExceptionInterface;

class ApiCallException extends \DomainException implements ApiCallExceptionInterface
{
    public function __construct(string $message = '', ResponseExceptionInterface $previous = null)
    {
        $message .= $previous->getMessage();
        parent::__construct($message, $previous->getCode(), $previous);
    }
}
