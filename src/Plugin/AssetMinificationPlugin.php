<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Plugin;

class AssetMinificationPlugin
{
    public function afterGetExcludes($_, array $result, string $contentType): array
    {
        return array_merge($result, $contentType === 'js' ? ['/Omikron_Factfinder/ff-web-components/'] : []);
    }
}
