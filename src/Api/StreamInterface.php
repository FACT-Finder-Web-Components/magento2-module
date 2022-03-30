<?php

declare(strict_types=1);

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

    /**
     * @return string
     */
    public function getContent(): string;

    /**
     * This method allows to add logic that should be executed after the feed is generated
     */
    public function finalize(): void;
}
