<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Cms;

use Omikron\Factfinder\Api\Export\DataProviderInterface;
use Omikron\Factfinder\Api\Export\ExportEntityInterface;
use Omikron\Factfinder\Api\Export\FieldInterface;

class DataProvider implements DataProviderInterface
{
    private Pages $pages;
    private PageFactory $pageFactory;
    private array $pageFields;

    public function __construct(Pages $pages, PageFactory $pageFactory, array $fields)
    {
        $this->pages       = $pages;
        $this->pageFactory = $pageFactory;
        $this->pageFields  = $fields;
    }

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
