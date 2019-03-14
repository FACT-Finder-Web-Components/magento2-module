<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export;

use Omikron\Factfinder\Api\Export\DataProviderInterface;
use Omikron\Factfinder\Api\ExporterInterface;
use Omikron\Factfinder\Api\StreamInterface;

class Feed
{
    /** @var ExporterInterface */
    private $exporter;

    /** @var DataProviderInterface */
    private $dataProvider;

    /** @var array */
    private $columns;

    public function __construct(ExporterInterface $exporter, DataProviderInterface $dataProvider, array $columns)
    {
        $this->exporter     = $exporter;
        $this->dataProvider = $dataProvider;
        $this->columns      = $columns;
    }

    public function generate(StreamInterface $stream): void
    {
        $stream->addEntity($this->columns);
        $this->exporter->exportEntities($stream, $this->dataProvider, $this->columns);
    }
}
