<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api;

use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Api\SessionDataInterface;
use Omikron\Factfinder\Model\Data\TrackingProduct;
use PHPUnit\Framework\TestCase;

class TrackingTest extends TestCase
{
    /** @var CommunicationConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $communicationConfigMock;

    /** @var ClientInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $factFinderClientMock;

    /** @var SessionDataInterface|\PHPUnit_Framework_MockObject_MockObject  */
    private $sessionDataMock;

    /** @var Tracking */
    private $tracking;

    /**
     * @dataProvider trackingProductProvider
     */
    public function test_execute_should_create_params_from_all_passed_products(...$trackingProducts)
    {
        $this->communicationConfigMock->method('getChannel')->willReturn('test-channel');
        $this->communicationConfigMock->method('getAddress')->willReturn('http://fake-factfinder.com/FACT-Finder-7.3');
        $this->sessionDataMock->method('getSessionId')->willReturn('some-session-id');
        $this->factFinderClientMock->expects($this->once())->method('sendRequest')
            ->with(
                'http://fake-factfinder.com/FACT-Finder-7.3/Tracking.ff',
                [
                    'event'    => 'checkout',
                    'channel'  => 'test-channel',
                    'products' => [
                        [
                            'id'       => '1',
                            'masterId' => '1',
                            'price'    => '39.99',
                            'count'    => 2,
                            'sid'      => 'some-session-id'
                        ],
                        [
                            'id'       => '2',
                            'masterId' => '2',
                            'price'    => '49.99',
                            'count'    => 2,
                            'sid'      => 'some-session-id'
                        ]
                    ]
                ]
            );

        $this->tracking->execute('checkout', ...$trackingProducts);
    }

    public function trackingProductProvider(): array
    {
        $trackingProducts = [
            new TrackingProduct('1', '1', '39.99',2),
            new TrackingProduct('2', '2', '49.99',2)
        ];

        return [$trackingProducts];
    }
    
    protected function setUp()
    {
        $this->communicationConfigMock = $this->createMock(CommunicationConfigInterface::class);
        $this->factFinderClientMock    = $this->createMock(ClientInterface::class);
        $this->sessionDataMock         = $this->createMock(SessionDataInterface::class);

        $this->tracking = new Tracking(
            $this->factFinderClientMock,
            $this->communicationConfigMock,
            $this->sessionDataMock
        );
    }
}
