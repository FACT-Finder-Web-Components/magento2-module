<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Ssr\Template;

use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\TemplateEngineInterface;
use Mustache_Engine as Mustache;

class Engine implements TemplateEngineInterface
{
    private Mustache $engine;

    public function __construct(Mustache $engine)
    {
        $this->engine = $engine;
    }

    public function render(BlockInterface $block, $templateFile, array $dictionary = [])
    {
        return $this->engine->loadTemplate($templateFile)->render($dictionary + ['block' => $block]);
    }
}
