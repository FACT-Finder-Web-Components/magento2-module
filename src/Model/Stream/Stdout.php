<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Stream;

use BadMethodCallException;
use Magento\Framework\Filesystem\DriverInterface;
use Omikron\Factfinder\Api\StreamInterface;

class Stdout implements StreamInterface
{
    public function __construct(private readonly DriverInterface $file) {}

    public function addEntity(array $entity): void
    {
        $this->file->filePutCsv(STDOUT, array_values($entity), ';', '"');
    }

    public function getContent(): string
    {
        throw new BadMethodCallException('Not implemented');
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    public function finalize(): void
    {
        //after export we need to exit, output has been sent to STDOUT so it is not possible to upload it
        //@phpcs:ignore Magento2.Security.LanguageConstruct.ExitUsage
        exit();
    }
}
