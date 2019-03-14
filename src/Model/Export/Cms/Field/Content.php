<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Cms\Field;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Email\Model\Template\Filter;
use Omikron\Factfinder\Api\Export\Cms\PageFieldInterface;

class Content implements PageFieldInterface
{
    /** @var Filter */
    private $filter;

    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    public function getValue(PageInterface $page): string
    {
        $filteredContent  = $this->filter->filter($page->getContent());
        $stylesAndScripts = '#\<(?:style|script)[^\>]*\>[^\<]*\</(?:style|script)\>#siU';
        $variables        = '#{{[^}]*}}#siU';
        $returns          = '#<br\s?\/?>#';
        $whitespaces      = '#(\s|&nbsp;)+#s';

        return preg_replace([$stylesAndScripts, $variables, $returns, $whitespaces], ' ', $filteredContent);
    }
}
