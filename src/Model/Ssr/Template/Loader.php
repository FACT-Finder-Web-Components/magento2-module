<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Ssr\Template;

use Mustache_Loader;
use Mustache_Source;
use Omikron\Factfinder\Api\Filter\FilterInterface;

class Loader implements Mustache_Loader
{
    private Mustache_Loader $loader;
    private FilterInterface $filter;

    public function __construct(Mustache_Loader $loader, FilterInterface $filter)
    {
        $this->loader = $loader;
        $this->filter = $filter;
    }

    /**
     * @inheritDoc
     */
    public function load($name)
    {
        $template = $this->loader->load($name);
        return $template instanceof Mustache_Source ? $template : $this->filter->filterValue($template);
    }
}
