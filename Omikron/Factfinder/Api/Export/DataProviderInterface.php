<?php

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
