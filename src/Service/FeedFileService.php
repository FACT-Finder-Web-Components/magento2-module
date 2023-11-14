<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Service;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use \InvalidArgumentException;

class FeedFileService
{
    private const FEED_FILENAME_PATTERN = 'export.%channel%.csv';

    /**
     * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
     * phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine
     */
    public function __construct(private readonly Filesystem $fileSystem) {}

    public function getFeedExportFilename(string $exportType, string $channel): string
    {
        if (empty($exportType)) {
            throw new InvalidArgumentException('Export type must not be empty');
        }

        if (empty($channel)) {
            throw new InvalidArgumentException('Channel must not be empty');
        }

        return str_replace(['%channel%'], [$channel], self::FEED_FILENAME_PATTERN);
    }

    public function getExportPath(string $fileName): string
    {
        return $this->fileSystem->getDirectoryWrite(DirectoryList::VAR_DIR)
            ->getAbsolutePath('factfinder' . DIRECTORY_SEPARATOR . $fileName);
    }
}
