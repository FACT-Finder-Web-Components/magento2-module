<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Ssr\Template;

use Mustache_Loader;
use Mustache_Source;
use Omikron\Factfinder\Api\Filter\FilterInterface;

class Loader implements Mustache_Loader
{
    /**
     * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
     * phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine
     */
    public function __construct(
        private readonly Mustache_Loader $loader,
        private readonly FilterInterface $filter,
    ) {}

    /**
     * @inheritDoc
     */
    public function load($name)
    {
        $template = $this->loader->load($name);
        return $template instanceof Mustache_Source ? $template : $this->filter->filterValue($template);
    }
}
