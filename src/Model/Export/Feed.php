<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export;

use Omikron\Factfinder\Api\Export\Catalog\ProductFieldInterface;
use Omikron\Factfinder\Api\ExporterInterface;
use Omikron\Factfinder\Api\StreamInterface;
use Omikron\Factfinder\Model\Export\Catalog\DataProviderFactory;
use Omikron\Factfinder\Model\Export\Catalog\ProductFieldProvider;

class Feed
{
    /** @var ExporterInterface */
    private $exporter;

    /** @var DataProviderFactory */
    private $dataProviderFactory;

    /** @var array */
    private $columns;

    /** @var ProductFieldProvider */
    private $fieldProvider;

    public function __construct(
        ExporterInterface $exporter,
        DataProviderFactory $dataProviderFactory,
        ProductFieldProvider $fieldProvider,
        array $columns
    ) {
        $this->exporter            = $exporter;
        $this->dataProviderFactory = $dataProviderFactory;
        $this->columns             = $columns;
        $this->fieldProvider       = $fieldProvider;
    }

    public function generate(StreamInterface $stream): void
    {
        $fields       = $this->fieldProvider->getFields();
        $columns      = $this->getColumns($fields);
        $dataProvider = $this->dataProviderFactory->create(['productFields' => $fields]);

        $stream->addEntity($columns);
        $this->exporter->exportEntities($stream, $dataProvider, $columns);
    }

    private function getColumns(array $fields): array
    {
        return array_merge($this->columns, array_map(function (ProductFieldInterface $field): string {
            return $field->getName();
        }, $fields));
    }
}
