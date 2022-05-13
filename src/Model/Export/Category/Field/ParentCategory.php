<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Category\Field;

use Magento\Framework\Model\AbstractModel;
use Omikron\Factfinder\Api\Export\FieldInterface;
use Omikron\Factfinder\Model\Formatter\CategoryPathFormatter;

class ParentCategory implements FieldInterface
{
    /** @var CategoryPathFormatter  */
    private $categoryPathFormatter;

    public function __construct(CategoryPathFormatter $categoryPathFormatter)
    {
        $this->categoryPathFormatter = $categoryPathFormatter;
    }

    public function getName(): string
    {
        return 'parentCategory';
    }

    public function getValue(AbstractModel $category): string
    {
        return $this->categoryPathFormatter->format((int) $category->getParentId(), $category->getStore());
    }
}
