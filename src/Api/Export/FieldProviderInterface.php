<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Api\Export;

/**
 * @api
 */
interface FieldProviderInterface
{
    /**
     * Method should return an array of fields which should be taken from configurable product
     *
     * @see FieldInterface
     *
     * @return array
     */
    public function getFields(): array;

    /**
     * Method should return an array of fields which should be taken from variants
     *
     * @see FieldInterface
     *
     * @return array
     */
    public function getVariantFields(): array;
}
