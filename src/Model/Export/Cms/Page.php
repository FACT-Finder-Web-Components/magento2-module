<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Cms;

use Magento\Cms\Api\Data\PageInterface;
use Omikron\Factfinder\Api\Export\ExportEntityInterface;
use Omikron\Factfinder\Api\Export\FieldInterface;

class Page implements ExportEntityInterface
{
    public function __construct(
        private readonly PageInterface $page,
        private readonly array $pageFields = [],
    ) {
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

        return array_reduce(
            $this->pageFields,
            fn (array $result, FieldInterface $field): array => [$field->getName() => $field->getValue($this->page)] + $result,
            $data
        );
    }
}
