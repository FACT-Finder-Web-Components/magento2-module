<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Cms;

use Magento\Cms\Api\Data\PageInterface;
use Omikron\Factfinder\Api\Export\ExportEntityInterface;
use Omikron\Factfinder\Api\Export\FieldInterface;

class Page implements ExportEntityInterface
{
    /** @var PageInterface */
    private $page;

    /** @var PageFieldInterface[] */
    private $pageFields;

    public function __construct(PageInterface $page, array $pageFields = [])
    {
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
            'PageId'          => (string) $this->getId(),
            'Master'          => (string) $this->getId(),
            'Identifier'      => (string) $this->page->getIdentifier(),
            'Title'           => (string) $this->page->getTitle(),
            'ContentHeading'  => (string) $this->page->getContentHeading(),
            'MetaKeywords'    => (string) $this->page->getMetaKeywords(),
            'MetaDescription' => (string) $this->page->getMetaDescription(),
        ];

        return array_reduce($this->pageFields, function (array $result, FieldInterface $field): array {
            return [$field->getName() => $field->getValue($this->page)] + $result;
        }, $data);
    }
}
