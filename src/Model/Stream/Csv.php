<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Stream;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\File\WriteInterface;
use Omikron\Factfinder\Api\StreamInterface;
use RuntimeException;
use SplFileObject;

class Csv implements StreamInterface
{
    private ?WriteInterface $stream;

    /**
     * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
     * phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine
     */
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly string     $filename = 'factfinder/export.csv'
    ) {}

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
        if (!isset($this->stream)) {
            $directory    = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
            $this->stream = $directory->openFile($directory->getAbsolutePath($this->filename), 'w+');
            $this->stream->lock();
        }

        return $this->stream;
    }

    public function finalize(): void
    {
        if ($this->countLines() <= 1) {
            throw new RuntimeException('Feed file is empty!');
        }
    }

    public function __destruct()
    {
        if ($this->stream) {
            $this->stream->unlock();
            $this->stream->close();
            $this->stream = null;
        }
    }

    private function countLines(): int
    {
        $directory = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $fileCheck = new SplFileObject($directory->getAbsolutePath($this->filename), 'r');
        $fileCheck->seek(PHP_INT_MAX);

        return $fileCheck->key();
    }
}
