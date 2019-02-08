<?php

declare(strict_types = 1);

namespace Omikron\Factfinder\Test\Unit\Model;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\HTTP\ClientFactory;
use Magento\Framework\HTTP\ClientInterface;
use Omikron\Factfinder\Api\Config\AuthConfigInterface;
use Omikron\Factfinder\Exception\ResponseException;
use Omikron\Factfinder\Model\Client;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ClientTest extends TestCase
{
    /** @var MockObject|ClientFactory\ */
    protected $clientFactoryMock;

    /** @var MockObject|SerializerInterface\ */
    protected $serializerMock;

    /** @var MockObject|AuthConfigInterface*/
    protected $authConfigMock;

    /** @var MockObject|ClientInterface */
    protected $curlClientMock;

    /** @var Client */
    protected $client;

    /**
     * @testdox ResponseException should be thrown if response body is not serializable
     */
    public function test_send_request_should_thrown_exception_when_response_is_not_serializable()
    {
        $this->serializerMock->expects($this->once())->method('unserialize')->willThrowException(new ResponseException());
        $this->curlClientMock->method('getStatus')->willReturn(200);
        $this->curlClientMock->method('getBody')->willReturn('unserializable string');

        $this->expectException('Omikron\Factfinder\Exception\ResponseException');

        $this->client->sendRequest('http://fake-ff-server.com/Search.ff', []);
    }

    /**
     * @testdox ResponseException should be thrown if response code is not equal to 200
     */
    public function test_send_request_should_throw_exception_if_status_is_not_200()
    {
        $this->curlClientMock->method('getStatus')->willReturn(500);
        $this->curlClientMock->method('getBody')->willReturn('unserializable string');

        $this->expectException('Omikron\Factfinder\Exception\ResponseException');

        $this->client->sendRequest('http://fake-ff-server.com/Search.ff', []);
    }

    /**
     * @testdox Correct response should be an associative array with 'searchResult' key
     */
    public function test_send_correct_request()
    {
        $response = '{"searchResult":{"breadCrumbTrailItems":[],"campaigns":[],"channel":"channel","fieldRoles":[]}}';
        $this->curlClientMock->method('getStatus')->willReturn(200);
        $this->curlClientMock->method('getBody')->willReturn($response);
        $this->serializerMock->expects($this->once())->method('unserialize')->willReturn(json_decode($response, true));

        $response = $this->client->sendRequest('http://fake-ff-server.com/Search.ff', []);

        $this->assertArrayHasKey('searchResult', $response, 'Correct response should contains searchResult key');
    }

    /**
     * @testdox The param username should be changed from OldUser to NewUser as it is contained in request params
     */
    public function test_override_params()
    {
        $defaultUserName = 'OldUser';
        $newUserName = 'NewUser';
        $endpoint = 'http://fact-finder-fake.com/FACT-Finder-7.3/Search.ff';
        $paramsFromRequest = ['username' => $newUserName];
        $this->authConfigMock->expects($this->never())->method('getUsername')->willReturn($defaultUserName);
        $this->curlClientMock->method('getStatus')->willReturn(200);
        $this->curlClientMock->expects($this->once())->method('get')->with($this->stringContains("username=$newUserName", false));
        $this->curlClientMock->method('getBody')->willReturn('{}');
        $this->serializerMock->expects($this->once())->method('unserialize')->willReturn([]);

        $this->client->sendRequest($endpoint, $paramsFromRequest);
    }

    protected function setUp()
    {
        $this->clientFactoryMock = $this->createMock(ClientFactory::class);
        $this->serializerMock = $this->createMock(SerializerInterface::class);
        $this->authConfigMock = $this->createMock(AuthConfigInterface::class);
        $this->curlClientMock = $this->createMock(ClientInterface::class);
        $this->clientFactoryMock->method('create')->willReturn($this->curlClientMock);

        $this->client = new Client(
            $this->clientFactoryMock,
            $this->serializerMock,
            $this->authConfigMock
        );
    }
}
