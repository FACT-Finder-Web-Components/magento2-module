<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Api\Export;

/**
 * @api
 */
interface DataProviderInterface
{
    /**
     * @return ExportEntityInterface[]
     */
    public function getEntities(): iterable;
}
