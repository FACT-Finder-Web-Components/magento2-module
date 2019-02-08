<?php

declare(strict_types = 1);

namespace Omikron\Factfinder\Model\Consumer\Tracking;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\Data\OrderItemInterface;
use Omikron\Factfinder\Model\Consumer\AbstractTracking;

class CreateOrder extends AbstractTracking
{
    /** @var string */
    protected $eventType = 'checkout';

    public function execute(Order $order) : void
    {
        $params = [
            'event'   => $this->eventType,
            'channel' => $this->communicationConfig->getChannel()
        ];
        $store  = $this->storeManager->getStore();

        /** @var OrderItemInterface $item */
        foreach ($order->getAllVisibleItems() as $item) {
            try {
                $product            = $this->productRepository->getById($item->getProductId());
                $productData         = [
                    'sid'      => $this->session->getSessionId(),
                    'id'       => $this->getTrackingProductNumber($product, $store),
                    'masterId' => $this->getMasterArticleNumber($product, $store),
                    'count'    => (int) $item->getQtyOrdered(),
                    'price'    => $this->getProductPrice($product, $store)
                ];
                $params['products'] []= $productData;
            } catch (NoSuchEntityException $e) {
                continue;
            }
        }

        $this->trackEvent($this->communicationConfig->getAddress() . '/' . $this->apiName, $params);
    }
}
