<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api;

use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Api\SessionDataInterface;
use Omikron\Factfinder\Model\Data\TrackingProduct;
use PHPUnit\Framework\Constraint\ArraySubset;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TrackingTest extends TestCase
{
    /** @var MockObject|ClientInterface */
    private $factFinderClientMock;

    /** @var MockObject|SessionDataInterface */
    private $sessionDataMock;

    /** @var Tracking */
    private $tracking;

    /**
     * @dataProvider trackingProductProvider
     */
    public function test_execute_should_create_params_from_all_passed_products(...$trackingProducts)
    {
        $this->sessionDataMock->method('getUserId')->willReturn(42);

        $this->factFinderClientMock->expects($this->once())->method('sendRequest')
            ->with('http://fake-factfinder.com/FACT-Finder-7.3/Tracking.ff', [
                'event'    => 'checkout',
                'channel'  => 'test-channel',
                'products' => [
                    [
                        'id'       => '1',
                        'masterId' => '1',
                        'price'    => '39.99',
                        'count'    => 2,
                        'sid'      => 'some-session-id',
                        'userId'   => 42,
                    ],
                    [
                        'id'       => '2',
                        'masterId' => '2',
                        'price'    => '49.99',
                        'count'    => 2,
                        'sid'      => 'some-session-id',
                        'userId'   => 42,
                    ],
                ],
            ]);

        $this->tracking->execute('checkout', ...$trackingProducts);
    }

    /**
     * @dataProvider trackingProductProvider
     */
    public function test_no_user_id_is_passed_when_the_customer_is_not_logged_in(...$trackingProducts)
    {
        $this->sessionDataMock->method('getUserId')->willReturn(0);

        $this->factFinderClientMock->expects($this->once())
            ->method('sendRequest')
            ->with($this->anything(), $this->logicalNot(new ArraySubset(['products' => [['userId' => 0]]])));

        $this->tracking->execute('checkout', ...$trackingProducts);
    }

    public function trackingProductProvider(): array
    {
        $trackingProducts = [
            new TrackingProduct('1', '1', '39.99', 2),
            new TrackingProduct('2', '2', '49.99', 2),
        ];
        return [$trackingProducts];
    }

    protected function setUp()
    {
        $this->factFinderClientMock = $this->createMock(ClientInterface::class);
        $this->sessionDataMock      = $this->createMock(SessionDataInterface::class);
        $this->sessionDataMock->method('getSessionId')->willReturn('some-session-id');

        $communicationConfig = $this->createConfiguredMock(CommunicationConfigInterface::class, [
            'getChannel' => 'test-channel',
            'getAddress' => 'http://fake-factfinder.com/FACT-Finder-7.3',
        ]);

        $this->tracking = new Tracking(
            $this->factFinderClientMock,
            $communicationConfig,
            $this->sessionDataMock
        );
    }
}
