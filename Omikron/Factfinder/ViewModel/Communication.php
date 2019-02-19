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

    public function getParameters(): array
    {
        return array_filter(
            $this->parametersProvider->getParameters(), function ($param) {
            return (bool) $param;
        }
        );
    }

    public function getFieldRoles(): string
    {
        return (string) $this->serializer->serialize($this->fieldRoles->getFieldRoles());
    }
}
