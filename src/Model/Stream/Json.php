<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Stream;

use Omikron\Factfinder\Api\StreamInterface;

class Json implements StreamInterface
{
    /** @var array */
    private $stream = [];

    public function addEntity(array $entity): void
    {
        $this->stream[] = $entity;
    }

    public function getContent(): string
    {
        return json_encode($this->stream);
    }
}
