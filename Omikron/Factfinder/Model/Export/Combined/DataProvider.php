<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Combined;

use Omikron\Factfinder\Api\Export\DataProviderInterface;

class DataProvider implements DataProviderInterface
{
    /** @var array */
    private $dataProviders;

    public function __construct(array $dataProviders)
    {
        $this->dataProviders = $dataProviders;
    }

    public function getEntities(): iterable
    {
        $combined = new \AppendIterator();
        foreach ($this->dataProviders as $provider) {
            $combined->append($provider->getEntities());
        }
        yield from $combined;
    }
}
