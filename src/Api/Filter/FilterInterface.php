<?php

namespace Omikron\Factfinder\Api\Filter;

/**
 * @api
 */
interface FilterInterface
{
    public function filterValue(string $value): string;
}
