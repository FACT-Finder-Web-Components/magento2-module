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
    /** @var ExporterInterface */
    private $exporter;

    /** @var DataProviderInterface */
    private $dataProvider;

    /** @var FieldInterface[] */
    private $fields;

    /** @var array */
    private $columns;

    public function __construct(
        ExporterInterface $exporter,
        DataProviderInterface $dataProvider,
        array $fields,
        array $columns
    ) {
        $this->exporter     = $exporter;
        $this->dataProvider = $dataProvider;
        $this->fields       = $fields;
        $this->columns      = $columns;
    }

    public function generate(StreamInterface $stream): void
    {
        $columns = $this->getColumns($this->fields);
        $stream->addEntity($columns);
        $this->exporter->exportEntities($stream, $this->dataProvider, $columns);
    }

    private function getColumns(array $fields): array
    {
        return array_values(array_unique(array_merge($this->columns, array_map([$this, 'getFieldName'], $fields))));
    }

    private function getFieldName(FieldInterface $field): string
    {
        return $field->getName();
    }
}
