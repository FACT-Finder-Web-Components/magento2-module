<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Api\Action;

/**
 * @api
 */
interface TrackingInterface
{
    public function execute(string $event, array $trackingProducts): void;
}
