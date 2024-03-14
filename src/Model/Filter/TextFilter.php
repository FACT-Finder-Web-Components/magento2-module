<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Filter;

use Omikron\Factfinder\Api\Filter\FilterInterface;

class TextFilter implements FilterInterface
{
    public function filterValue(string $value): string
    {
        // phpcs:ignore
        $tags  = '#<(address|article|aside|blockquote|br|canvas|dd|div|dl|dt|fieldset|figcaption|figure|footer|form|h[1-6]|header|hr|li|main|nav|noscript|ol|p|pre|section|table|tfoot|ul|video)#';
        $value = preg_replace($tags, ' <$1', $value); // Add one space in front of block elements before stripping tags
        $value = strip_tags($value);
        $value = htmlentities($value);
        $value = htmlspecialchars_decode($value);
        $value = preg_replace('#\s+#u', ' ', $value);
        $value = preg_replace('#[[:^print:]]#u', '', $value);
        return trim($value);
    }
}
