<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Category;

use Magento\Catalog\Api\Data\CategoryInterface;
use Omikron\Factfinder\Api\Export\ExportEntityInterface;
use Omikron\Factfinder\Api\Export\FieldInterface;

class Category implements ExportEntityInterface
{
    /**
     * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
     * phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine
     */
    public function __construct(
        private readonly CategoryInterface $category,
        private readonly array $categoryFields,
    ) {}

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

        return array_reduce(
            $this->categoryFields,
            fn (array $result, FieldInterface $field): array =>
                [$field->getName() => $field->getValue($this->category)] + $result,
            $data
        );
    }
}
