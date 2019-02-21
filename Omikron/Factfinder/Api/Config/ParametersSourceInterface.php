<?php

namespace Omikron\Factfinder\Api\Config;

interface ParametersSourceInterface
{
    public function getParameters(): array;
}
