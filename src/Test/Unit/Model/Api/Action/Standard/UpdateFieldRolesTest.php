<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api\Action\Standard;

use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\ClientInterfaceFactory;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Exception\ResponseException;
use Omikron\Factfinder\Model\Api\Credentials;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UpdateFieldRolesTest extends TestCase
{
    /** @var MockObject|FieldRolesInterface */
    private $fieldRolesMock;

    /** @var MockObject|ClientInterface */
    private $factFinderClientMock;

    /** @var UpdateFieldRoles */
    private $updateFieldRoles;

    public function test_execute_should_return_true_if_response_contain_field_roles()
    {
        $response = ['searchResult' => ['fieldRoles' => ['masterArticleNumber' => 'sku']]];
        $this->factFinderClientMock->expects($this->once())->method('get')->willReturn($response);
        $this->fieldRolesMock->expects($this->once())->method('saveFieldRoles')->with(['masterArticleNumber' => 'sku']);
        $this->assertTrue($this->updateFieldRoles->execute(1));
    }

    public function test_execute_should_throw_an_exception_if_response_does_not_contain_field_roles()
    {
        $this->factFinderClientMock->expects($this->once())->method('get')->willReturn([]);
        $this->expectException(ResponseException::class);
        $this->updateFieldRoles->execute(1);
    }

    protected function setUp(): void
    {
        $communicationConfigMock = $this->createConfiguredMock(CommunicationConfigInterface::class, [
            'getChannel' => 'test-channel',
            'getAddress' => 'http://fake-fact-finder.com/Fact-Finder-7.3',
        ]);

        $this->fieldRolesMock       = $this->createMock(FieldRolesInterface::class);
        $this->factFinderClientMock = $this->createMock(ClientInterface::class);
        $clientFactoryMock          = $this->getMockBuilder(ClientInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $clientFactoryMock->method('create')->willReturn($this->factFinderClientMock);

        $this->updateFieldRoles = new UpdateFieldRoles(
            $clientFactoryMock,
            $this->fieldRolesMock,
            $communicationConfigMock,
            $this->createMock(Credentials::class)
        );
    }
}
