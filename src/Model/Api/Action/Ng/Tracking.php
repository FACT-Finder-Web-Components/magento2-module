<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api\Ng\Action;

use Omikron\Factfinder\Api\Action\TrackingInterface;
use Omikron\Factfinder\Api\ClientInterfaceFactory;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Api\Data\TrackingProductInterface;
use Omikron\Factfinder\Api\SessionDataInterface;
use Omikron\Factfinder\Model\Api\Credentials;

class Tracking implements TrackingInterface
{
    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    /** @var ClientInterfaceFactory */
    private $clientFactory;

    /** @var SessionDataInterface */
    private $sessionData;

    /** @var Credentials */
    private $credentials;

    public function __construct(
        ClientInterfaceFactory $clientFactory,
        CommunicationConfigInterface $communicationConfig,
        SessionDataInterface $sessionData,
        Credentials $credentials
    ) {
        $this->clientFactory       = $clientFactory;
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
        $endpoint = $this->communicationConfig->getAddress()
            . sprintf('/rest/v3/track/%s/%s', $this->communicationConfig->getChannel(), $event);
        $params   = array_map(function (TrackingProductInterface $trackingProduct) {
            return array_filter([
                                    'id'       => $trackingProduct->getTrackingNumber(),
                                    'masterId' => $trackingProduct->getMasterArticleNumber(),
                                    'price'    => $trackingProduct->getPrice(),
                                    'count'    => $trackingProduct->getCount(),
                                    'sid'      => $this->sessionData->getSessionId(),
                                    'userId'   => $this->sessionData->getUserId(),
                                ]);
        }, $trackingProducts);

        $this->clientFactory->create()
            ->setHeaders(['Authorization' => $this->credentials->toBasicAuth()])
            ->post($endpoint, $params);
    }
}
