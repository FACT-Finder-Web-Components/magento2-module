<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Omikron\Factfinder\Model\Config\CommunicationParametersProvider;
use Omikron\Factfinder\Model\FieldRoles;

class Communication implements ArgumentInterface
{
    /** @var FieldRoles */
    private $fieldRoles;

    /** @var SerializerInterface */
    private $serializer;

    /** @var CommunicationParametersProvider */
    private $parametersProvider;

    /** @var string[] */
    private $mergeableParams;

    public function __construct(
        FieldRoles $fieldRoles,
        SerializerInterface $serializer,
        CommunicationParametersProvider $parametersProvider,
        array $mergeableParams = ['add-params', 'add-tracking-params', 'keep-url-params', 'parameter-whitelist']
    ) {
        $this->parametersProvider = $parametersProvider;
        $this->fieldRoles         = $fieldRoles;
        $this->serializer         = $serializer;
        $this->mergeableParams    = array_combine($mergeableParams, array_fill(0, count($mergeableParams), ''));
    }

    public function getParameters(array $blockParams = []): array
    {
        $params = $this->parametersProvider->getParameters();
        return ['version' => $params['version'] ?? 'ng'] + array_filter($this->mergeParameters($blockParams, $params) + $blockParams + $params, 'boolval');
    }

    public function getFieldRoles(): string
    {
        return (string) $this->serializer->serialize($this->fieldRoles->getFieldRoles());
    }

    private function mergeParameters(array ...$params): array
    {
        $params = array_map(function (array $param): array {
            return array_intersect_key($param + $this->mergeableParams, $this->mergeableParams);
        }, $params);

        return array_reduce(array_keys($this->mergeableParams), function ($result, $key) use ($params) {
            return $result + [$key => implode(',', array_filter(array_column($params, $key)))];
        }, []);
    }
}
