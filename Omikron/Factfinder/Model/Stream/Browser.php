<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Stream;

use Omikron\Factfinder\Api\StreamInterface;

class Browser implements StreamInterface
{
   /** @var resource */
    private $stream;

    public function __construct(string $fileName)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $fileName);
    }

    public function addEntity(array $entity): void
    {
        fputcsv($this->getStream(), array_values($entity), ';', '"');
    }

    public function getContent(): string
    {
        throw new \BadMethodCallException('Not implemented');
    }

    public function dispose(): bool
    {
        @fclose($this->stream);
        @flock($this->stream,LOCK_UN);
        return true;
    }

    private function getStream()
    {
        if (!$this->stream) {
            $this->stream = fopen('php://output', 'w');
            flock($this->stream, LOCK_EX);
        }
        return $this->stream;
    }
}
