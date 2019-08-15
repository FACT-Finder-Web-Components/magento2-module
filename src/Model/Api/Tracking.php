<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api;

use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Api\Data\TrackingProductInterface;
use Omikron\Factfinder\Api\SessionDataInterface;

class Tracking
{
    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    /** @var ClientInterface */
    private $factFinderClient;

    /** @var SessionDataInterface */
    private $sessionData;

    /** @var string */
    private $apiName = 'Tracking.ff';

    public function __construct(
        ClientInterface $factFinderClient,
        CommunicationConfigInterface $communicationConfig,
        SessionDataInterface $sessionData
    ) {
        $this->factFinderClient    = $factFinderClient;
        $this->communicationConfig = $communicationConfig;
        $this->sessionData         = $sessionData;
    }

    public function execute(string $event, TrackingProductInterface ...$trackingProducts)
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

        $this->factFinderClient->sendRequest($this->communicationConfig->getAddress() . '/' . $this->apiName, $params);
    }
}
