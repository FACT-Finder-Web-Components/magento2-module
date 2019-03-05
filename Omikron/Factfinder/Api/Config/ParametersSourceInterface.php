<?php

namespace Omikron\Factfinder\Api\Config;

interface ParametersSourceInterface
{
    /**
     * Get configuration parameters for the <ff-communication> component
     *
     * @return array
     */
    public function getParameters(): array;
}
