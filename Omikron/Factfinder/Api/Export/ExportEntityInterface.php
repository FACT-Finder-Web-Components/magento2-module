<?php

namespace Omikron\Factfinder\Api\Export;

/**
 * @api
 */
interface ExportEntityInterface
{
    /**
     * Convert entity data to associative array
     *
     * @param array $attributes Optionally specify which attributes should be exported
     *
     * @return array
     */
    public function toArray(array $attributes = []): array;
}
