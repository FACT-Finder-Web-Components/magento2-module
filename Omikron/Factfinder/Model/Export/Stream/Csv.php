<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Stream;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\File\WriteInterface as FileWriteInterface;
use Omikron\Factfinder\Api\Export\StreamInterface;

class Csv implements StreamInterface
{
    private const FEED_DIRECTORY_PATH = 'factfinder';

    /** @var Filesystem */
    private $fileSystem;

    /** @var FileWriteInterface|null */
    private $stream;

    /** @var string */
    private $fileName;

    public function __construct(Filesystem $fileSystem, string $fileName = 'export.csv')
    {
        $this->fileSystem = $fileSystem;
        $this->fileName   = $fileName;
    }

    public function addEntity(array $entity): void
    {
        if (!$this->stream) {
            $this->initStream();
        }
        $this->stream->writeCsv($entity);
    }

    public function __destruct()
    {
        $this->stream->unlock();
        $this->stream->close();
    }

    private function initStream(): void
    {
        $fileDirectoryPath = $this->fileSystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $fileDirectoryPath->create(self::FEED_DIRECTORY_PATH);
        $this->stream = $fileDirectoryPath->openFile($fileDirectoryPath->getAbsolutePath(self::FEED_DIRECTORY_PATH) . '/' . $this->fileName, 'w+');
        $this->stream->lock();
    }
}
