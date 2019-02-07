<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Consumer\Tracking;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Omikron\Factfinder\Model\Consumer\AbstractTracking;

class OrderTracking extends AbstractTracking
{
    /** @var string */
    protected $eventType = 'checkout';

    public function execute(OrderInterface $order) : void
    {
        // start with mandatory get parameters
        $params           = http_build_query(['event' => $this->eventType, 'channel' => $this->communicationConfig->getChannel()]);
        $store            = $this->storeManager->getStore();
        $paramsCollection = [];

        /** @var OrderItemInterface $item */
        foreach ($order->getItems() as $item) {
            if ($item->getIsVirtual()) {
                try {
                    $product            = $this->productRepository->getById($item->getProductId());
                    $paramsItem         = [
                        'sid'      => $this->session->getSessionId(),
                        'id'       => $this->getTrackingProductNumber($product, $store),
                        'masterId' => $this->getMasterArticleNumber($product, $store),
                        'count'    => (int) $item->getQtyOrdered(),
                        'price'    => $this->getProductPrice($product, $store)
                    ];
                    $paramsCollection[] = http_build_query($paramsItem);
                } catch (NoSuchEntityException $e) {
                    continue;
                }
            }
        }

        // concatenate get parameters of ordered articles
        $params .= '&' . implode('&', $paramsCollection);

        // track checkout event
        $this->factFinderClient->sendRequest($this->communicationConfig->getAddress() . $this->apiName, $params);
    }
}
