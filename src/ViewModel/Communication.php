<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Model\Config\CommunicationParametersProvider;

class Communication implements ArgumentInterface
{
    /** @var FieldRolesInterface */
    private $fieldRoles;

    /** @var SerializerInterface */
    private $serializer;

    /** @var CommunicationParametersProvider */
    private $parametersProvider;

    public function __construct(
        FieldRolesInterface $fieldRoles,
        SerializerInterface $serializer,
        CommunicationParametersProvider $parametersProvider
    ) {
        $this->parametersProvider = $parametersProvider;
        $this->fieldRoles         = $fieldRoles;
        $this->serializer         = $serializer;
    }

    public function getParameters(array $blockParams = []): array
    {
        return array_map(function ($element) {
            return is_array($element) ? $this->mergeParameters($element) : $element;
        }, array_filter(array_merge_recursive($blockParams, $this->parametersProvider->getParameters()), 'boolval'));
    }

    public function getFieldRoles(): string
    {
        return (string)$this->serializer->serialize($this->fieldRoles->getFieldRoles());
    }

    private function mergeParameters(array $params): string
    {
        return !empty(array_intersect(['true', 'false'], $params)) ? $params[0] : implode(',', $params);
    }
}
