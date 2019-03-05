<?php

namespace Omikron\Factfinder\Api;

use Omikron\Factfinder\Api\Export\DataProviderInterface;
use Omikron\Factfinder\Api\Export\StreamInterface;

/**
 * @api
 */
interface ExporterInterface
{
    /**
     * @param StreamInterface         $stream
     * @param DataProviderInterface[] $dataProviders
     * @param string[]                $columns
     */
    public function exportEntities(StreamInterface $stream, array $dataProviders, array $columns): void;
}
