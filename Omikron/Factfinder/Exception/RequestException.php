<?php

namespace Omikron\Factfinder\Exception;

use Omikron\Factfinder\Api\RequestExceptionInterface;

class RequestException extends \DomainException implements RequestExceptionInterface
{
    /** @var string */
    protected $responseBody;

    public function setResponseBody(string $body = '') : RequestExceptionInterface
    {
        $this->responseBody = $body;

        return $this;
    }

    public function getResponseBody() : string
    {
        return $this->responseBody;
    }
}
