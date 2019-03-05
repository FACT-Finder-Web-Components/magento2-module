<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Omikron\Factfinder\Api\Export\DataProviderInterface;
use Omikron\Factfinder\Api\Export\StreamInterface;
use Omikron\Factfinder\Api\ExporterInterface;

class Exporter implements ExporterInterface
{
    /**
     * @inheritdoc
     */
    public function exportEntities(StreamInterface $stream, DataProviderInterface $dataProvider, array $columns): void
    {
        $entityData = array_combine($columns, array_fill(0, count($columns), ''));
        foreach ($dataProvider->getEntities() as $entity) {
            $stream->addEntity(array_merge($entityData, array_intersect_key($entity->toArray(), $entityData)));
        }
    }
}
