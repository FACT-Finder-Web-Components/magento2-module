<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Combined;

use Omikron\Factfinder\Api\Export\DataProviderInterface;

class DataProvider implements DataProviderInterface
{
    /** @var DataProviderInterface[] */
    private $dataProviders;

    public function __construct(array $dataProviders)
    {
        $this->dataProviders = $dataProviders;
    }

    public function getEntities(): iterable
    {
        yield from [];
        foreach ($this->dataProviders as $provider) {
            yield from $provider->getEntities();
        }
    }
}
