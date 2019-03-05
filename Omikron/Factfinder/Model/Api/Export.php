<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api;

use Omikron\Factfinder\Api\Export\DataProviderInterface;
use Omikron\Factfinder\Api\Export\StreamInterface;
use Omikron\Factfinder\Api\ExporterInterface;
use Omikron\Factfinder\Api\Filter\FilterInterface;

class Export implements ExporterInterface
{
    /** @var FilterInterface  */
    private $filter;

    /** @var array  */
    private $columns;

    public function __construct(FilterInterface $filter, array $columns)
    {
        $this->filter = $filter;
        $this->columns = $columns;
    }

    public function exportEntities(StreamInterface $stream, DataProviderInterface $dataProvider, array $columns = []): void
    {
        $columns = array_merge($this->columns, $columns);
        $stream->addEntity($columns);
        foreach ($dataProvider->getEntities() as $entity) {
            $stream->addEntity(array_map([$this->filter, 'filterValue'], $this->prepareForExport($entity->toArray(), $columns)));
        }
    }

    private function prepareForExport(array $entityData, array $attributes = []): array
    {
        if (!$attributes) {
            return $entityData;
        }
        $data = array_combine($attributes, array_fill(0, count($attributes), ''));
        return array_merge($data, array_intersect_key($entityData, $data));
    }
}
