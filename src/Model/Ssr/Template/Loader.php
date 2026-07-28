<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Ssr\Template;

use Handlebars\HandlebarsString;
use Handlebars\Loader as HandlebarsLoader;
use Omikron\Factfinder\Api\Filter\FilterInterface;

class Loader implements HandlebarsLoader
{
    public function __construct(
        private readonly HandlebarsLoader $loader,
        private readonly FilterInterface $filter,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function load($name)
    {
        $template = $this->loader->load($name);

        $filteredTemplate = $this->filter->filterValue((string) $template);

        return new HandlebarsString($filteredTemplate);
    }
}
