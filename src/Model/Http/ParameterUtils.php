<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Http;

class ParameterUtils
{
    public function fixedGetParams(array $params): array
    {
        return array_combine(array_map(function (string $key): string {
            return preg_match('#^filter(.*?)ROOT/#', $key) ? str_replace('_', '+', $key) : $key;
        }, array_keys($params)), array_values($params));
    }
}
