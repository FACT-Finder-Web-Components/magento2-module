<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Cms;

use Omikron\Factfinder\Api\Export\DataProviderInterface;
use Omikron\Factfinder\Api\Export\ExportEntityInterface;
use Omikron\Factfinder\Api\Export\FieldInterface;

class DataProvider implements DataProviderInterface
{
    public function __construct(
        private readonly Pages $pages,
        private readonly  PageFactory $pageFactory,
        private readonly  array $fields
    ) {}

    /**
     * @return ExportEntityInterface[]
     */
    public function getEntities(): iterable
    {
        yield from [];
        foreach ($this->pages as $page) {
            yield $this->pageFactory->create(['page' => $page, 'pageFields' => $this->pageFields]);
        }
    }
}
