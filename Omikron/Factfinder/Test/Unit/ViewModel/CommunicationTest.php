<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\ViewModel;

use PHPUnit\Framework\TestCase;
use Magento\Framework\Serialize\SerializerInterface;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Model\Config\CommunicationParametersProvider;
use PHPUnit\Framework\MockObject\MockObject;

class CommunicationTest extends TestCase
{
    /** @var MockObject|FieldRolesInterface */
    private $fieldRolesMock;

    /** @var MockObject|SerializerInterface */
    private $serializerMock;

    /** @var MockObject|CommunicationParametersProvider */
    private $parametersProviderMock;

    private $communication;

    public function test_get_parameters_filter_null_values()
    {
        $this->parametersProviderMock->method('getParameters')->willReturn(
            [
                'url'     => 'http://some-url',
                'version' => '7.3',
                'user-id' => null,
                'channel' => 'some-channel'
            ]
        );

        $parameters = $this->communication->getParameters();

        $this->assertArrayNotHasKey('user-sid', $parameters);
    }

    protected function setUp()
    {
        $this->parametersProviderMock = $this->createMock(CommunicationParametersProvider::class);
        $this->fieldRolesMock         = $this->createMock(FieldRolesInterface::class);
        $this->serializerMock         = $this->createMock(SerializerInterface::class);
        $this->communication          = new \Omikron\Factfinder\ViewModel\Communication(
            $this->fieldRolesMock,
            $this->serializerMock,
            $this->parametersProviderMock
        );
    }
}
