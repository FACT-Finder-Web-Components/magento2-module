<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Data;

use Omikron\Factfinder\Api\Data\TrackingProductInterface;

class TrackingProduct implements TrackingProductInterface
{
    /** @var string */
    private $trackingNumber;

    /** @var string */
    private $masterArticleNumber;

    /** @var string */
    private $price;

    /** @var int */
    private $count;

    public function __construct(string $trackingNumber, string $masterArticleNumber, string $price, int $count)
    {
        $this->trackingNumber      = $trackingNumber;
        $this->masterArticleNumber = $masterArticleNumber;
        $this->price               = $price;
        $this->count               = $count;
    }

    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }

    public function getMasterArticleNumber(): string
    {
        return $this->masterArticleNumber;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
