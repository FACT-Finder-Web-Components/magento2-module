<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Http;

class ParameterUtils
{
    public function fixedGetParams(array $params): array
    {
        return array_combine(array_map(function (string $key): string {
            // changing + to spaces to be compatible with PHP_QUERY_RFC1738
            // @link https://www.php.net/manual/en/function.http-build-query.php
            return preg_match('#^filter(.*?)ROOT/#', $key) ? str_replace('_', ' ', $key) : $key;
        }, array_keys($params)), array_values($params));
    }
}
