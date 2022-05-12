<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Category;

use Magento\Catalog\Api\Data\CategoryInterface;
use Omikron\Factfinder\Api\Export\ExportEntityInterface;
use Omikron\Factfinder\Api\Export\FieldInterface;

class Category implements ExportEntityInterface
{
    /** @var CategoryInterface */
    private $category;

    /** @var array */
    private $categoryFields;

    public function __construct(CategoryInterface $category, array $categoryFields)
    {
        $this->category       = $category;
        $this->categoryFields = $categoryFields;
    }

    public function getId(): int
    {
        return $this->category->getId();
    }

    public function toArray(): array
    {
        $data = [
            'Name'        => (string) $this->category->getName(),
            'sourceField' => 'CategoryPath',
            'Deeplink'    => (string) $this->category->getUrl(),
        ];

        return array_reduce($this->categoryFields, function (array $result, FieldInterface $field) {
            return [$field->getName() => $field->getValue($this->category)] + $result;
        }, $data);
    }
}
