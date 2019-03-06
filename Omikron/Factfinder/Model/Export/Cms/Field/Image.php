<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Cms\Field;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Email\Model\Template\Filter;
use Omikron\Factfinder\Api\Export\Cms\PageFieldInterface;

class Image implements PageFieldInterface
{
    /** @var Filter */
    private $filter;

    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    public function getValue(PageInterface $page): string
    {
        preg_match('/(http:\/\/|https:\/\/)[a-zA-Z0-9\.\/_]+\.(jpg|png)/', $this->filter->filter($page->getContent()), $result);

        return $result[0] ?? '';
    }
}
