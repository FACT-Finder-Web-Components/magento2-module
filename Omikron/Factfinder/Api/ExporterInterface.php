<?php

namespace Omikron\Factfinder\Api;

use Omikron\Factfinder\Api\Export\DataProviderInterface;
use Omikron\Factfinder\Api\Export\StreamInterface;

/**
 * @api
 */
interface ExporterInterface
{
    public function exportEntities(StreamInterface $stream, DataProviderInterface $dataProvider, array $columns = []): void;
}
