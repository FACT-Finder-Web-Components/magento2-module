<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Cms;

use Omikron\Factfinder\Api\Export\DataProviderInterface;
use Omikron\Factfinder\Api\Export\ExportEntityInterface;

class DataProvider implements DataProviderInterface
{
    /** @var Pages */
    private $pages;

    /** @var PageFactory */
    private $pageFactory;

    public function __construct(Pages $pages, PageFactory $pageFactory)
    {
        $this->pages       = $pages;
        $this->pageFactory = $pageFactory;
    }

    /**
     * @return ExportEntityInterface[]
     */
    public function getEntities(): iterable
    {
        foreach ($this->pages as $page) {
            yield $this->pageFactory->create(['page' => $page]);
        }
    }
}
