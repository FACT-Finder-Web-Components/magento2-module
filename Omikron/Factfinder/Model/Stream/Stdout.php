<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Stream;

use Omikron\Factfinder\Api\Export\StreamInterface;

class Stdout implements StreamInterface
{
    public function addEntity(array $entity): void
    {
        fputcsv(STDOUT, array_values($entity), ';', '"');
    }
}
