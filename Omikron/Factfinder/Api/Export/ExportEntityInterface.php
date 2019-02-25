<?php

namespace Omikron\Factfinder\Api\Export;

/**
 * @api
 */
interface ExportEntityInterface
{
    /**
     * Get entity ID
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Convert entity data to associative array
     *
     * @return array
     */
    public function toArray(): array;
}
