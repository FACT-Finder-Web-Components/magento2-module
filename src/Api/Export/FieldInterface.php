<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Api\Export;
use Magento\Framework\Model\AbstractModel;

/**
 * @api
 */
interface FieldInterface
{
    public function getName(): string;

    public function getValue(AbstractModel $model): string;
}
