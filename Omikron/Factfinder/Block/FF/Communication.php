<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\FF;

use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Xml\Parser;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Model\ComponentBuilder;
use Omikron\Factfinder\Model\Config\CommunicationParametersProvider;

class Communication extends Template
{
    /** @var Parser */
    private $parser;

    /** @var FieldRolesInterface */
    private $fieldRoles;

    /** @var SerializerInterface */
    private $serializer;

    /** @var CommunicationParametersProvider */
    private $communicationParametersProvider;

    /** @var ComponentBuilder  */
    private $componentBuilder;

    public function __construct(
        Context $context,
        Parser $parser,
        FieldRolesInterface $fieldRoles,
        SerializerInterface $serializer,
        CommunicationParametersProvider $communicationParametersProvider,
        ComponentBuilder $componentBuilder,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->communicationParametersProvider = $communicationParametersProvider;
        $this->parser                          = $parser;
        $this->fieldRoles                      = $fieldRoles;
        $this->serializer                      = $serializer;
        $this->componentBuilder                = $componentBuilder;
    }

    public function getWebComponent(): string
    {
        $parameters = array_filter($this->communicationParametersProvider->getParameters(), function($element) {
            return (bool) $element;
        }) + $this->getLayoutOverrides();

        return $this->componentBuilder->buildComponent('ff-communication', $parameters);
    }

    public function getFieldRoles(): string
    {
        return (string) $this->serializer->serialize($this->fieldRoles->getFieldRoles());
    }

    private function getLayoutOverrides(): array
    {
        return $this->getData('communication_parameters') ?? [];
    }
}
