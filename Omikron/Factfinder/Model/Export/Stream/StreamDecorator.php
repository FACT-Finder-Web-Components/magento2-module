<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Stream;

use Omikron\Factfinder\Api\Export\StreamInterface;

abstract class StreamDecorator implements StreamInterface
{
    protected $decoratedStream;

    public function __construct(StreamInterface $decoratedStream)
    {
        $this->decoratedStream = $decoratedStream;
    }

    abstract public function addEntity(array $entity): void;
}
