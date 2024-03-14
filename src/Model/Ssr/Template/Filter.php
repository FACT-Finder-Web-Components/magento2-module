<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Ssr\Template;

use Omikron\Factfinder\Api\Filter\FilterInterface;
use Omikron\Factfinder\Model\FieldRoles;

class Filter implements FilterInterface
{
    /**
     * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
     * phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine
     */
    public function __construct(private readonly FieldRoles $fieldRoles)
    {
    }

    public function filterValue(string $value): string
    {
        $value = preg_replace('#data-anchor="([^"]+?)"#', 'href="$1" $0', $value);
        $value = preg_replace('#data-redirect-target="_(blank|self|parent|top)"#', 'target="_$1" $0', $value);
        return preg_replace_callback('#data-image(?:="([^"]+?)")?#', function (array $match): string {
            $imageField = $this->fieldRoles->getFieldRole('imageUrl');
            return sprintf('src="%s" %s', $match[1] ?? "{{record.{$imageField}}}", $match[0]);
        }, $value);
    }
}
