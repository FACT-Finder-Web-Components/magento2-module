<?php

namespace Omikron\Factfinder\Api\Export;

/**
 * @api
 */
interface DataProvider
{
    /**
     * @return ExportEntity[]
     */
    public function getEntities(): iterable;
}
