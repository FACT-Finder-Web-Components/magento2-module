<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Api;

use Omikron\Factfinder\Api\Export\DataProviderInterface;

/**
 * @api
 */
interface ExporterInterface
{
    /**
     * @param StreamInterface       $stream
     * @param DataProviderInterface $dataProvider
     * @param string[]              $columns
     */
    public function exportEntities(StreamInterface $stream, DataProviderInterface $dataProvider, array $columns): void;
}
