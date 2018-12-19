<?php

namespace Omikron\Factfinder\Plugin;

use Magento\Framework\View\Asset\Minification;

/**
 * Plugin to exclude web components JavaScript files from minification
 *
 * @package Omikron\Factfinder\Plugin
 */
class ExcludeFilesFromMinification
{
    public function afterGetExcludes(Minification $subject, array $result, $contentType)
    {
        if ($contentType == 'js') {
            $result[] = 'Omikron_Factfinder/ff-web-components/dist/elements.build';
        }
        return $result;
    }
}
