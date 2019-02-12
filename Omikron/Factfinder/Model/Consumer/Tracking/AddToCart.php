<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Consumer\Tracking;

use Magento\Catalog\Api\Data\ProductInterface;
use Omikron\Factfinder\Exception\ResponseException;
use Omikron\Factfinder\Model\Consumer\AbstractTracking;

class AddToCart extends AbstractTracking
{
    /** @var string  */
    protected $eventType = 'cart';

    /**
     * @param ProductInterface $product
     * @param int              $amount
     * @return void
     * @throws ResponseException
     */
    public function execute(ProductInterface $product, int $amount): void
    {
        $store  = $this->storeManager->getStore();
        $params = [
            'event'    => $this->eventType,
            'id'       => $this->getTrackingProductNumber($product, $store),
            'masterId' => $this->getMasterArticleNumber($product, $store),
            'price'    => $this->getProductPrice($product, $store),
            'count'    => (int) round($amount),
            'sid'      => $this->session->getSessionId(),
            'channel'  => $this->communicationConfig->getChannel(),
        ];

        if ($userId = $this->session->getCustomerId()) {
            $params['userId'] = $userId;
        }

        $this->factFinderClient->sendRequest($this->communicationConfig->getAddress() . '/' . $this->apiName, $params);
    }
}
