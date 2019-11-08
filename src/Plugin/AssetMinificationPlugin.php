<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Plugin;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class AssetMinificationPlugin
{
    /** Web Components library location */
    const JS_LIBRARY = '/Omikron_Factfinder/ff-web-components/';

    public function afterGetExcludes($subject, array $result, string $contentType): array
    {
        return array_merge($result, $contentType === 'js' ? [self::JS_LIBRARY] : []);
    }
}
