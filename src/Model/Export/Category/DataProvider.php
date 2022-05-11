<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Category;

use Omikron\Factfinder\Api\Export\DataProviderInterface;

class DataProvider implements DataProviderInterface
{
    /** @var Categories */
    private $categories;

    /** @var CategoryFactory */
    private $categoryFactory;

    /** @var array */
    private $fields;

    public function __construct(
        Categories      $categories,
        CategoryFactory $categoryFactory,
        array           $fields
    ) {
        $this->categories      = $categories;
        $this->categoryFactory = $categoryFactory;
        $this->fields          = $fields;
    }

    public function getEntities(): iterable
    {
        yield from [];
        foreach ($this->categories as $category) {
            yield $this->categoryFactory->create(['category'       => $category,
                                                  'categoryFields' => $this->fields
                                                 ]);
        }
    }
}
