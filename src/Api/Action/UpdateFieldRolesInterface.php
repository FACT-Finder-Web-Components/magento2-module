<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Api\Action;

/**
 * @api
 */
interface UpdateFieldRolesInterface
{
    public function execute(int $scopeId = null, array $params = []): bool;
}
