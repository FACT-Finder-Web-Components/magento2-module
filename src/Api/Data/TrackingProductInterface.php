<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Api\Data;

/**
 * Data object carrying data between shop system and Tracking Event service
 *
 * @api
 */
interface TrackingProductInterface
{
    public function getTrackingNumber(): string;

    public function getMasterArticleNumber(): string;

    public function getPrice(): string;

    public function getCount(): int;
}
