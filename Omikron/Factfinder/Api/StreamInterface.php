<?php

namespace Omikron\Factfinder\Api;

/**
 * @api
 */
interface StreamInterface
{
    /**
     * @param array $entity
     */
    public function addEntity(array $entity): void;

    public function getContent(): string;

    public function dispose(): bool;
}
