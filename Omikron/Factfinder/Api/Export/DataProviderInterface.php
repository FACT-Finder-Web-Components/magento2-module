<?php

namespace Omikron\Factfinder\Api\Export;

/**
 * @api
 */
interface DataProviderInterface
{
    /**
     * @return ExportEntity[]
     */
    public function getEntities(): iterable;
}
