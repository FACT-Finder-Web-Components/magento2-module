<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Api\Filter;

interface FilterInterface
{
    public function filterValue(string $value): string;
}
