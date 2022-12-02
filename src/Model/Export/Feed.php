<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export;

use Omikron\Factfinder\Api\Export\DataProviderInterface;
use Omikron\Factfinder\Api\Export\FieldInterface;
use Omikron\Factfinder\Api\Export\FieldProviderInterface;
use Omikron\Factfinder\Api\ExporterInterface;
use Omikron\Factfinder\Api\StreamInterface;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 */
class Feed
{
    public function __construct(
        private readonly ExporterInterface $exporter,
        private readonly DataProviderInterface $dataProvider,
        private readonly array $fields,
        private readonly array $columns
    ) {}

    public function generate(StreamInterface $stream): void
    {
        $columns = $this->getColumns($this->fields);
        $stream->addEntity($columns);
        $this->exporter->exportEntities($stream, $this->dataProvider, $columns);
        $stream->finalize();
    }

    private function getColumns(array $fields): array
    {
        return array_values(array_unique([...$this->columns, ...array_map([$this, 'getFieldName'], $fields)]));
    }

    private function getFieldName(FieldInterface $field): string
    {
        return $field->getName();
    }
}
