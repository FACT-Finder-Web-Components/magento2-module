<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Stream;

use Omikron\Factfinder\Api\StreamInterface;

class Stdout implements StreamInterface
{
    public function addEntity(array $entity): void
    {
        fputcsv(STDOUT, array_values($entity), ';', '"');
    }

    public function getContent(): string
    {
        throw new \BadMethodCallException('Not implemented');
    }

    public function dispose(): bool
    {
        return true;
    }
}
