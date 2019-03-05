<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Omikron\Factfinder\Api\Export\DataProviderInterface;
use Omikron\Factfinder\Api\Export\StreamInterface;
use Omikron\Factfinder\Api\ExporterInterface;

class Exporter implements ExporterInterface
{
    /** @var array  */
    private $columns;

    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * @inheritdoc
     */
    public function exportEntities(StreamInterface $stream, array $dataProviders, array $columns = []): void
    {
        $columns = array_merge($this->columns, $columns);
        $entityData = array_combine($columns, array_fill(0, count($columns), ''));
        /** @var DataProviderInterface $dataProvider */
        foreach ($dataProviders as $dataProvider) {
            foreach ($dataProvider->getEntities() as $entity) {
                $stream->addEntity(array_merge($entityData, array_intersect_key($entity->toArray(), $entityData)));
            }
        }
    }
}
