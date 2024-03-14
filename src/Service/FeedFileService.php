<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Service;

use InvalidArgumentException;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class FeedFileService
{
    private const FEED_FILENAME_PATTERN = 'export.%type%.%channel%.csv';

    public function __construct(private readonly Filesystem $fileSystem)
    {
    }

    public function getFeedExportFilename(string $exportType, string $channel): string
    {
        if (empty($exportType)) {
            throw new InvalidArgumentException('Export type must not be empty');
        }

        if (empty($channel)) {
            throw new InvalidArgumentException('Channel must not be empty');
        }

        return str_replace(['%type%', '%channel%'], [$exportType, $channel], self::FEED_FILENAME_PATTERN);
    }

    public function getExportPath(string $fileName): string
    {
        return $this->fileSystem->getDirectoryWrite(DirectoryList::VAR_DIR)
            ->getAbsolutePath('factfinder' . DIRECTORY_SEPARATOR . $fileName);
    }
}
