<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Stream;

use BadMethodCallException;
use Magento\Framework\Filesystem\DriverInterface;
use Omikron\Factfinder\Api\StreamInterface;

class Stdout implements StreamInterface
{
    private DriverInterface $file;

    public function __construct(DriverInterfac $file)
    {
        $this->file = $file;
    }

    public function addEntity(array $entity): void
    {
        $this->file->filePutCsv(STDOUT, array_values($entity), ';', '"');
    }

    public function getContent(): string
    {
        throw new BadMethodCallException('Not implemented');
    }
}
