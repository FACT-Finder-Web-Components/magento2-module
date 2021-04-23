<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Cms\Field;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Email\Model\Template\Filter;
use Magento\Framework\Model\AbstractModel;
use Omikron\Factfinder\Api\Export\FieldInterface;

class Content implements FieldInterface
{
    /** @var Filter */
    private $filter;

    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    public function getName(): string
    {
        return 'Content';
    }

    /**
     * @param PageInterface $page
     * @return string
     */
    public function getValue(AbstractModel $page): string
    {
        $filteredContent  = $this->filter->filter($page->getContent());
        $stylesAndScripts = '#\<(?:style|script)[^\>]*\>[^\<]*\</(?:style|script)\>#siU';
        $variables        = '#{{[^}]*}}#siU';
        $returns          = '#<br\s?\/?>#';
        $whitespaces      = '#(\s|&nbsp;)+#s';

        return preg_replace([$stylesAndScripts, $variables, $returns, $whitespaces], ' ', $filteredContent);
    }
}
