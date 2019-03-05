<?php

namespace Omikron\Factfinder\Api\Export;

/**
 * @api
 */
interface StreamInterface
{
    /**
     * @param array $entity
     */
    public function addEntity(array $entity): void;
}
