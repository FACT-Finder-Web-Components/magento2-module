<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Model;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\HTTP\ClientFactory;
use Magento\Framework\HTTP\ClientInterface;
use Omikron\Factfinder\Api\Config\AuthConfigInterface;
use Omikron\Factfinder\Exception\ResponseException;
use Omikron\Factfinder\Model\Api\Credentials;
use Omikron\Factfinder\Model\Api\CredentialsFactory;
use Omikron\Factfinder\Model\Client;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ClientTest extends TestCase
{
    /** @var MockObject|SerializerInterface */
    private $serializerMock;

    /** @var MockObject|ClientInterface */
    private $httpClientMock;

    /** @var Client */
    private $client;

    /**
     * @testdox ResponseException should be thrown if response body is not serializable
     */
    public function test_send_request_should_thrown_exception_when_response_is_not_serializable()
    {
        $this->httpClientMock->method('getStatus')->willReturn(200);
        $this->httpClientMock->method('getBody')->willReturn('unserializable string');
        $this->serializerMock->expects($this->once())->method('unserialize')->willThrowException(new ResponseException());

        $this->expectException(ResponseException::class);
        $this->client->sendRequest('http://fake-ff-server.com/Search.ff', []);
    }

    /**
     * @testdox ResponseException should be thrown if response code is not equal to 200
     */
    public function test_send_request_should_throw_exception_if_status_is_not_200()
    {
        $this->httpClientMock->method('getStatus')->willReturn(500);
        $this->httpClientMock->method('getBody')->willReturn('{}');

        $this->expectException(ResponseException::class);
        $this->client->sendRequest('http://fake-ff-server.com/Search.ff', []);
    }

    /**
     * @testdox correct response should be an associative array with 'searchResult' key
     */
    public function test_send_correct_request()
    {
        $response = '{"searchResult":{"breadCrumbTrailItems":[],"campaigns":[],"channel":"channel","fieldRoles":[]}}';
        $this->httpClientMock->method('getStatus')->willReturn(200);
        $this->httpClientMock->method('getBody')->willReturn($response);
        $this->serializerMock->expects($this->once())->method('unserialize')->willReturn(json_decode($response, true));

        $response = $this->client->sendRequest('http://fake-ff-server.com/Search.ff', []);

        $this->assertArrayHasKey('searchResult', $response, 'Correct response should contains searchResult key');
    }

    /**
     * @testdox the API credentials can be overwritten using request params
     */
    public function test_override_params()
    {
        $newUserName = 'OverrideUser';

        $this->httpClientMock->method('getStatus')->willReturn(200);
        $this->httpClientMock->expects($this->once())->method('get')->with($this->stringContains("username=$newUserName"));
        $this->httpClientMock->method('getBody')->willReturn('{}');
        $this->serializerMock->expects($this->once())->method('unserialize')->willReturn([]);

        $this->client->sendRequest('http://fake-ff-server.com/Search.ff', ['username' => $newUserName]);
    }

    protected function setUp()
    {
        $this->serializerMock = $this->createMock(SerializerInterface::class);
        $this->httpClientMock = $this->createMock(ClientInterface::class);

        /** @var CredentialsFactory $credentialsFactory */
        $credentialsFactory = $this->getMockBuilder(CredentialsFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $credentialsFactory->method('create')->willReturn(new Credentials('apiUser', 'apiPassword', 'FF', 'FF'));

        $this->client = new Client(
            $this->createConfiguredMock(ClientFactory::class, ['create' => $this->httpClientMock]),
            $this->serializerMock,
            $this->createMock(AuthConfigInterface::class),
            $credentialsFactory
        );
    }
}
