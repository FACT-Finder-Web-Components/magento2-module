<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductField;

class NumericalAttributes extends FilterAttributes
{
    protected string $name = 'NumericalAttributes';
    protected bool $numerical = true;
}
