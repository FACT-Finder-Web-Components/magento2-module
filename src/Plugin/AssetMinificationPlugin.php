<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Plugin;

class AssetMinificationPlugin
{
    public function aroundGetExcludes($_, callable $proceed, string $contentType): array
    {
        return array_merge($proceed($contentType), [
            '/Omikron_Factfinder/ff-web-components/',
        ]);
    }
}
