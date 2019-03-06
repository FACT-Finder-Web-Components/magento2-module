<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Cms\Field;

use Magento\Cms\Api\Data\PageInterface;
use Omikron\Factfinder\Api\Export\Cms\PageFieldInterface;

class Image implements PageFieldInterface
{
    public function getValue(PageInterface $page): string
    {
        preg_match('/(http:\/\/|https:\/\/)[a-zA-Z0-9\.\/_]+\.(jpg|png)/', $page->getContent(), $result);
        return $result[0] ?? '';
    }
}
