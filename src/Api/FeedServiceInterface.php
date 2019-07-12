<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Api;

use Omikron\Factfinder\Model\Stream\Csv;

/**
 * Interface FeedServiceInterface
 * Use to integrate feed with FACT-Finder. It exports, uploads feed and triggers import
 *
 * @api
 */
interface FeedServiceInterface
{
    /**
     * Integrate feed file with FACT-Finder
     *
     * @param int $storeId
     */
    public function integrate(int $storeId): void;

    /**
     * @param int $storeId
     *
     * @return Csv feed file
     */
    public function get(int $storeId): Csv;
}
