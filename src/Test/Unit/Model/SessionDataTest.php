<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Omikron\Factfinder\Api\SessionDataInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SessionDataTest extends TestCase
{
    /** @var SessionData */
    private $sessionData;

    /** @var MockObject|CustomerSession */
    private $sessionMock;

    /** @var MockObject|ScopeConfigInterface */
    private $scopeConfigMock;

    /** @var RemoteAddress|ScopeConfigInterface */
    private $remoteAddressMock;

    public function test_it_implements_the_session_data_interface()
    {
        $this->assertInstanceOf(SessionDataInterface::class, $this->sessionData);
    }

    /**
     * @testdox User ID is fetched from the customer session
     */
    public function test_not_logged_in()
    {
        $this->sessionMock->method('getCustomerId')->willReturn(123456);
        $this->assertSame(123456, $this->sessionData->getUserId());
    }

    /**
     * @testdox User ID is 0 if the customer is not logged in
     */
    public function test_not_logged_id()
    {
        $this->assertSame(0, $this->sessionData->getUserId());
    }

    /**
     * @testdox      The session ID is correctly calculated (30 characters long)
     * @dataProvider sessionIdProvider
     */
    public function test_session_id(string $from, string $to)
    {
        $this->sessionMock->method('getSessionId')->willReturn($from);
        $this->assertSame($to, $this->sessionData->getSessionId());
    }

    public function test_it_implements_the_customer_section_source_interface()
    {
        $this->assertInstanceOf(SectionSourceInterface::class, $this->sessionData);
    }

    public function test_it_collects_the_customer_data()
    {
        $this->sessionMock->method('getCustomerId')->willReturn(123456);
        $this->sessionMock->method('getSessionId')->willReturn('7ddf32e17a6ac5ce04a8ecbf782ca5');

        $expected = ['uid' => 123456, 'sid' => '7ddf32e17a6ac5ce04a8ecbf782ca5'];
        $this->assertArraySubset($expected, $this->sessionData->getSectionData());
    }

    /**
     * @dataProvider remoteAddressProvider
     */
    public function test_it_tracks_internal_requests(string $config, string $address, bool $result)
    {
        $this->scopeConfigMock->method('getValue')->willReturn($config);
        $this->remoteAddressMock->method('getRemoteAddress')->willReturn($address);

        $expected = ['internal' => $result];
        $this->assertArraySubset($expected, $this->sessionData->getSectionData());
    }

    public function sessionIdProvider()
    {
        return [
            ['shorter', 'shortershortershortershortersh'],
            [sha1('longer'), substr(sha1('longer'), 0, 30)],
            ['', 'a415ab5cc17c8c093c015ccdb7e552'],
        ];
    }

    public function remoteAddressProvider()
    {
        return [
            ['127.0.0.1,8.8.8.8', '127.0.0.1', true],
            ['127.0.0.1,8.8.8.8', '4.4.4.4', false],
        ];
    }

    protected function setUp()
    {
        $this->sessionMock       = $this->createMock(CustomerSession::class);
        $this->scopeConfigMock   = $this->createMock(ScopeConfigInterface::class);
        $this->remoteAddressMock = $this->createMock(RemoteAddress::class);

        $this->sessionData = new SessionData($this->sessionMock, $this->scopeConfigMock, $this->remoteAddressMock);
    }
}

function uniqid(): string
{
    return 'random';
}
