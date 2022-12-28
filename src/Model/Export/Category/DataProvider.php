<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Category;

use Omikron\Factfinder\Api\Export\DataProviderInterface;

class DataProvider implements DataProviderInterface
{
    /**
     * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
     * phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine
     */
    public function __construct(
        private readonly Categories      $categories,
        private readonly CategoryFactory $categoryFactory,
        private readonly array           $fields
    ) {}

    public function getEntities(): iterable
    {
        yield from [];
        foreach ($this->categories as $category) {
            yield $this->categoryFactory->create(
                [
                    'category'       => $category,
                    'categoryFields' => $this->fields
                ]
            );
        }
    }
}
