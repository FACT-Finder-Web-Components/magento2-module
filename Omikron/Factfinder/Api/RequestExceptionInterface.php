<?php

declare(strict_types = 1);

namespace Omikron\Factfinder\Api;

interface RequestExceptionInterface
{
    public function setResponseBody(string $body) : RequestExceptionInterface;

    public function getResponseBody() : string;
}
