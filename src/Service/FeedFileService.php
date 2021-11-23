<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Service;

use Exception;

class FeedFileService
{
    private const FEED_FILENAME_PATTERN = 'export.%type%.%channel%.csv';

    /**
     * @param string $exportType
     * @param string $channel
     * @return string
     * @throws Exception
     */
    public function getFeedExportFilename(string $exportType, string $channel): string
    {
        if (empty($exportType)) {
            throw new \Exception('Export type should not be empty');
        }

        if (empty($channel)) {
            throw new \Exception('Channel should not be empty');
        }

        return str_replace(['%type%', '%channel%'], [$exportType, $channel], self::FEED_FILENAME_PATTERN);
    }
}
