<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Api\Config;

/**
 * @api
 */
interface FeatureConfigInterface
{
    /**
     * Use FF to render category pages?
     *
     * @return bool
     */
    public function useForCategories(): bool;
}
