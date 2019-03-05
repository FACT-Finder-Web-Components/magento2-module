<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Cms\Field;

use Magento\Cms\Api\Data\PageInterface;
use Omikron\Factfinder\Api\Export\Cms\PageFieldInterface;

class Id implements PageFieldInterface
{
    private const CMS_PAGE_ID_PREFIX = 'P';

    public function getValue(PageInterface $page): string
    {
        return self::CMS_PAGE_ID_PREFIX . $page->getId();
    }
}
