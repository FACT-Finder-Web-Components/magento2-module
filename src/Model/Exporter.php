<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Omikron\Factfinder\Api\Export\DataProviderInterface;
use Omikron\Factfinder\Api\ExporterInterface;
use Omikron\Factfinder\Api\Filter\FilterInterface;
use Omikron\Factfinder\Api\StreamInterface;

class Exporter implements ExporterInterface
{
    public function __construct(private readonly FilterInterface $filter) {}

    public function exportEntities(StreamInterface $stream, DataProviderInterface $dataProvider, array $columns): void
    {
        $emptyRecord = array_combine($columns, array_fill(0, count($columns), ''));
        foreach ($dataProvider->getEntities() as $entity) {
            $stream->addEntity($this->prepareRow($entity->toArray(), $emptyRecord));
        }
    }

    private function prepareRow(array $entityData, array $emptyRecord): array
    {
        return array_map([$this->filter, 'filterValue'], [...$emptyRecord, ...array_intersect_key($entityData, $emptyRecord)]);
    }
}
