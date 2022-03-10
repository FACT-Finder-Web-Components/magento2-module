<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Service;

use Exception;
use Magento\Framework\Exception\InvalidArgumentException;

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
            throw new InvalidArgumentException(__('Argument $exportType must not be empty'));
        }

        if (empty($channel)) {
            throw new InvalidArgumentException(__('Argument $channel must not be empty'));
        }

        return str_replace(['%type%', '%channel%'], [$exportType, $channel], self::FEED_FILENAME_PATTERN);
    }
}
