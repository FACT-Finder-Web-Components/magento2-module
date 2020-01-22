<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Cms\Field;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Email\Model\Template\Filter as TemplateFilter;
use Omikron\Factfinder\Api\Export\Cms\PageFieldInterface;

class Image implements PageFieldInterface
{
    /** @var TemplateFilter */
    private $filter;

    public function __construct(TemplateFilter $filter)
    {
        $this->filter = $filter;
    }

    public function getValue(PageInterface $page): string
    {
        $pattern = '#https?://[^/\s]+/\S+\.(jpe?g|png|gif)#';
        preg_match($pattern, $this->filter->filter((string) $page->getContent()), $result);
        return $result[0] ?? '';
    }
}
