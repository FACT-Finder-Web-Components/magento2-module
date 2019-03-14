<?php

namespace Omikron\Factfinder\Api\Export\Cms;

use Magento\Cms\Api\Data\PageInterface;

/**
 * @api
 */
interface PageFieldInterface
{
    public function getValue(PageInterface $page): string;
}
