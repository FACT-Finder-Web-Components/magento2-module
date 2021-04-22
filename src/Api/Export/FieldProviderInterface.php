<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Api\Export;

/**
 * @api
 */
interface FieldProviderInterface
{
    /**
     * Method should return an array where
     * a keys represents the field names and the values
     * are a FieldInterface implementation responsible
     * for retrieving corresponding product value
     *
     * @see FieldInterface
     *
     * @return array
     */
    public function getFields(): array;
}
