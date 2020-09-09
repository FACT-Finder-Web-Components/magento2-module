<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api\Action\Standard;

use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\Action\TrackingInterface;
use Omikron\Factfinder\Model\Api\ClientFactory;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Api\Data\TrackingProductInterface;
use Omikron\Factfinder\Api\SessionDataInterface;
use Omikron\Factfinder\Model\Api\Credentials;

class Tracking implements TrackingInterface
{
    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    /** @var ClientInterface */
    private $factFinderClient;

    /** @var SessionDataInterface */
    private $sessionData;

    /** @var Credentials */
    private $credentials;

    /** @var string */
    private $apiName = 'Tracking.ff';

    public function __construct(
        ClientInterface $factFinderClient,
        CommunicationConfigInterface $communicationConfig,
        SessionDataInterface $sessionData,
        Credentials $credentials
    ) {
        $this->factFinderClient    = $factFinderClient;
        $this->communicationConfig = $communicationConfig;
        $this->sessionData         = $sessionData;
        $this->credentials         = $credentials;
    }

    /**
     * @param string                     $event
     * @param TrackingProductInterface[] $trackingProducts
     */
    public function execute(string $event, array $trackingProducts): void
    {
        $params = [
            'event'    => $event,
            'channel'  => $this->communicationConfig->getChannel(),
            'products' => array_map(function (TrackingProductInterface $trackingProduct) {
                return array_filter([
                    'id'       => $trackingProduct->getTrackingNumber(),
                    'masterId' => $trackingProduct->getMasterArticleNumber(),
                    'price'    => $trackingProduct->getPrice(),
                    'count'    => $trackingProduct->getCount(),
                    'sid'      => $this->sessionData->getSessionId(),
                    'userId'   => $this->sessionData->getUserId(),
                ]);
            }, $trackingProducts),
        ];

        $this->factFinderClient->get($this->communicationConfig->getAddress() . '/' . $this->apiName, $this->credentials->toArray() + $params);
    }
}
