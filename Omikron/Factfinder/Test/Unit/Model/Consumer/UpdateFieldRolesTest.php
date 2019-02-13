<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Consumer;

use Magento\Framework\Serialize\SerializerInterface;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Exception\ResponseException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UpdateFieldRolesTest extends TestCase
{
    /** @var MockObject|FieldRolesInterface */
    private $fieldRolesMock;

    /** @var MockObject|ClientInterface */
    private $factFinderClientMock;

    /** @var MockObject|CommunicationConfigInterface */
    private $communicationConfigMock;

    /** @var MockObject|SerializerInterface */
    private $serializerMock;

    /** @var UpdateFieldRoles */
    private $updateFieldRoles;

    public function test_execute_should_return_true_if_response_contain_field_roles()
    {
        $response = ['searchResult' => ['fieldRoles' => ['masterArticleNumber' => 'sku']]];
        $this->factFinderClientMock->expects($this->once())->method('sendRequest')->willReturn($response);
        $this->serializerMock->expects($this->once())->method('serialize')->willReturn(json_encode($response));
        $this->assertTrue($this->updateFieldRoles->execute(1));
    }

    public function test_execute_should_throw_an_exception_if_response_does_not_contain_field_roles()
    {
        $this->factFinderClientMock->expects($this->once())->method('sendRequest')->willReturn(['searchResult' => []]);
        $this->expectException(ResponseException::class);
        $this->updateFieldRoles->execute(1);
    }

    protected function setUp()
    {
        $this->fieldRolesMock          = $this->createMock(FieldRolesInterface::class);
        $this->factFinderClientMock    = $this->createMock(ClientInterface::class);
        $this->communicationConfigMock = $this->createMock(CommunicationConfigInterface::class);
        $this->serializerMock          = $this->createMock(SerializerInterface::class);

        $this->communicationConfigMock->method('getChannel')->willReturn('test-channel');
        $this->communicationConfigMock->method('getAddress')->willReturn('http://fake-fact-finder.com/Fact-Finder-7.3');

        $this->updateFieldRoles = new UpdateFieldRoles(
            $this->fieldRolesMock,
            $this->factFinderClientMock,
            $this->communicationConfigMock,
            $this->serializerMock
        );
    }
}
