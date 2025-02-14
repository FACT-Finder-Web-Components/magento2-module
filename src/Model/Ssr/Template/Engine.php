<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Ssr\Template;

use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\TemplateEngineInterface;
use Handlebars\Handlebars;

class Engine implements TemplateEngineInterface
{
    public function __construct(private readonly Handlebars $engine)
    {
    }

    public function render(BlockInterface $block, $templateFile, array $dictionary = []): string
    {
        return $this->engine->loadTemplate($templateFile)->render($dictionary + ['block' => $block]);
    }
}
