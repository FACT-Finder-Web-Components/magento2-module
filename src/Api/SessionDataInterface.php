<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Api;

/**
 * Session information interface
 *
 * @api
 */
interface SessionDataInterface
{
    /**
     * Get user ID, if available, or 0 if not
     *
     * @return int
     */
    public function getUserId(): int;
}
