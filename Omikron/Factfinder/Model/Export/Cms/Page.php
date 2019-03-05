<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Cms;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\Data\StoreInterface;
use Omikron\Factfinder\Api\Export\Cms\PageFieldInterface;
use Omikron\Factfinder\Api\Export\ExportEntityInterface;

class Page implements ExportEntityInterface
{
    /** @var PageInterface */
    private $page;

    /** @var array */
    private $pageFields;

    public function __construct(
        PageInterface $page,
        array $pageFields = []
    ) {
        $this->page       = $page;
        $this->pageFields = $pageFields;
    }

    public function getId(): int
    {
        return (int) $this->page->getId();
    }

    public function toArray(): array
    {
        $data = [
            'PageIdentifier'      => (string) $this->page->getIdentifier(),
            'PageTitle'           => (string) $this->page->getTitle(),
            'PageContentHeading'  => (string) $this->page->getContentHeading(),
            'PageMetaKeywords'    => (string) $this->page->getMetaKeywords(),
            'PageMetaDescription' => (string) $this->page->getMetaDescription(),
        ];

        return array_merge($data, array_map(function (PageFieldInterface $field): string {
            return $field->getValue($this->page);
        }, $this->pageFields));
    }
}
