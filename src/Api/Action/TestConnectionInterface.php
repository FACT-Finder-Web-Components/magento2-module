<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Api\Action;

/**
 * @api
 */
interface TestConnectionInterface
{
    public function execute(string $serverUrl, array $params): bool;
}
