<?php


namespace Omikron\Factfinder\Service;


class FeedFileService
{
    private const FEED_FILENAME_PATTERN = 'export.%type%.%channel%.csv';

    public function getFeedExportFilename(string $type, string $channel): string
    {
        return str_replace(['%type%', '%channel%'], [$type, $channel], self::FEED_FILENAME_PATTERN);
    }
}
