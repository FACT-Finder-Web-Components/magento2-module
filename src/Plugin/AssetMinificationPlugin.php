<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Plugin;

class AssetMinificationPlugin
{
    const JS_LIBRARY = '/Omikron_Factfinder/ff-web-components/';

    public function afterGetExcludes($_, array $result, string $contentType): array
    {
        return array_merge($result, $contentType === 'js' ? [self::JS_LIBRARY] : []);
    }
}
