<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Export;

use Omikron\Factfinder\Controller\Export\Product;

class Cms extends Product
{
    /** @var string */
    protected $feedType = 'cms';
}
