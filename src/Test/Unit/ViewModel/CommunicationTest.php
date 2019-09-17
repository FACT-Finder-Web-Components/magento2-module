<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Framework\Serialize\SerializerInterface;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Model\Config\CommunicationParametersProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CommunicationTest extends TestCase
{
    /** @var MockObject|FieldRolesInterface */
    private $fieldRolesMock;

    /** @var MockObject|SerializerInterface */
    private $serializerMock;

    /** @var MockObject|CommunicationParametersProvider */
    private $parametersProviderMock;

    /** @var Communication */
    private $communication;

    public function test_get_parameters_filter_null_values()
    {
        $this->parametersProviderMock->method('getParameters')->willReturn([
            'url'       => 'http://some-url',
            'version'   => '7.3',
            'user-id'   => null,
            'channel'   => 'some-channel',
            'use-cache' => 'true'
        ]);
        $this->assertArrayNotHasKey('user-sid', $this->communication->getParameters());
    }

    public function test_get_parameters_will_implode_arrays()
    {
        $blockParams = ['add-params' => 'param1=123'];
        $parameters  = $this->communication->getParameters($blockParams);

        $this->assertArrayHasKey('add-params', $parameters);
        $this->assertEquals('param1=123,param2=abc', $parameters['add-params']);
    }

    public function test_multiple_boolean_values_will_be_overwritten()
    {
        $blockParams = ['use-cache' => 'false'];
        $parameters  = $this->communication->getParameters($blockParams);

        $this->assertArrayHasKey('use-cache', $parameters);
        $this->assertEquals('false', $parameters['use-cache']);
    }

    protected function setUp()
    {
        $this->parametersProviderMock = $this->createMock(CommunicationParametersProvider::class);
        $this->fieldRolesMock         = $this->createMock(FieldRolesInterface::class);
        $this->serializerMock         = $this->createMock(SerializerInterface::class);

        $this->parametersProviderMock->method('getParameters')->willReturn([
            'url'        => 'http://some-url',
            'version'    => '7.3',
            'user-id'    => null,
            'channel'    => 'some-channel',
            'use-cache'  => 'true',
            'add-params' => 'param2=abc',
        ]);

        $this->communication = new Communication(
            $this->fieldRolesMock,
            $this->serializerMock,
            $this->parametersProviderMock
        );
    }
}
