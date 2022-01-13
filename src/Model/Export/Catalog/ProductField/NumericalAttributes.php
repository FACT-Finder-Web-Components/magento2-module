<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductField;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Model\AbstractModel;

class NumericalAttributes extends FilterAttributes
{
    protected string $name = 'NumericalAttributes';
    protected bool $numerical = true;
}
