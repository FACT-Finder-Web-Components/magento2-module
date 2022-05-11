<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Category\Field;

use Magento\Framework\Model\AbstractModel;
use Omikron\Factfinder\Api\Export\FieldInterface;

class Deeplink implements FieldInterface
{
    public function getName(): string
    {
        return 'Deeplink';
    }

    public function getValue(AbstractModel $category): string
    {
        return (string) $category->getUrl();
    }
}
