<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config;

use Omikron\Factfinder\Api\Config\ParametersSourceInterface;

class CommunicationParametersProvider implements ParametersSourceInterface
{
    /** @var array  */
    private $parameterSources;

    public function __construct(array $parametersSource = [])
    {
        $this->parameterSources = $parametersSource;
    }

    public function getParameters(): array
    {
        $params = [];
        foreach ($this->parameterSources as $source) {
            if (!$source instanceof ParametersSourceInterface) {
                throw new \InvalidArgumentException(sprintf('Parameters source does not implement %s interface',ParametersSourceInterface::class));
            }
            $params = array_merge($params, $source->getParameters());
        }

        return $params;
    }
}
