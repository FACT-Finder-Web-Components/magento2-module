<?php

namespace Omikron\Factfinder\Model\Stream;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\File\WriteInterface;
use Omikron\Factfinder\Api\StreamInterface;

class Csv implements StreamInterface
{
    /** @var Filesystem */
    private $filesystem;

    /** @var WriteInterface|null */
    private $stream;

    /** @var string */
    private $filename;

    public function __construct(Filesystem $filesystem, string $filename = 'factfinder/export.csv')
    {
        $this->filesystem = $filesystem;
        $this->filename   = $filename;
    }

    /**
     * @param array $entity
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function addEntity(array $entity): void
    {
        $this->getStream()->writeCsv($entity, ';', '"');
    }

    public function getContent(): string
    {
        return $this->getStream()->readAll();
    }

    private function getStream(): WriteInterface
    {
        if (!$this->stream) {
            $directory    = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
            $this->stream = $directory->openFile($directory->getAbsolutePath($this->filename), 'w+');
            $this->stream->lock();
        }
        return $this->stream;
    }

    public function __destruct()
    {
        if ($this->stream) {
            $this->stream->unlock();
            $this->stream->close();
            $this->stream = null;
        }
    }
}
