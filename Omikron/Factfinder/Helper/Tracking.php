<?php

declare(strict_types = 1);

namespace Omikron\Factfinder\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Sales\Model\Order;
use Omikron\Factfinder\Api\ClientInterface;
use Magento\Customer\Model\Session;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;

/**
 * Class Tracking
 * Helper Class for sending tracking events to FF
 */
class Tracking extends AbstractHelper
{
    const API_NAME = 'Tracking.ff';
    const EVENT_TYPE_CART = 'cart';
    const EVENT_TYPE_CHECKOUT = 'checkout';

    protected $factFinderClient;

    /** @var Product */
    protected $product;

    /** @var StoreInterface */
    protected $store;

    /** @var Session  */
    protected $session;

    /** @var Data */
    protected $configHelper;

    /** @var Communication */
    protected $communicationConfig;

    public function __construct(
        Context $context,
        ClientInterface $factFinderClient,
        Product $product,
        StoreManagerInterface $storeManager,
        Session $session,
        Data $configHelper,
        CommunicationConfigInterface $communicationConfig
    ) {
        $this->factFinderClient    = $factFinderClient;
        $this->product             = $product;
        $this->session             = $session;
        $this->configHelper        = $configHelper;
        $this->communicationConfig = $communicationConfig;
        $this->store               = $storeManager->getStore();

        parent::__construct($context);
    }

    /**
     * Send tracking events for add to cart actions to FF
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param integer $amount
     */
    public function addToCart($product, $amount)
    {
        $params = [
            'event' => self::EVENT_TYPE_CART,
            'id' => $this->product->get($this->configHelper->getFieldRole('trackingProductNumber'), $product, $this->store),
            'masterId' => $this->product->get($this->configHelper->getFieldRole('masterArticleNumber'), $product, $this->store),
            'price' => $this->product->get('Price', $product, $this->store),
            'count' => (int) round($amount),
            'sid' => $this->getSessionId(),
            'channel' => $this->getChannel(),
        ];

        if (!is_null($userId = $this->getUserId())) {
            $params['userId'] = $userId;
        }

        // track cart event
        $this->factFinderClient->sendRequest($this->communicationConfig->getAddress() . self::API_NAME, $params);
    }

    /**
     * Sends tracking events for checkout to FF
     *
     * @param Order $order
     */
    public function checkout($order)
    {
        // start with mandatory get parameters
        $params = http_build_query([
            'event' => self::EVENT_TYPE_CHECKOUT,
            'channel' => $this->getChannel(),
        ]);

        $sid = $this->getSessionId();
        $trackingProductNumberFieldRole = $this->configHelper->getFieldRole('trackingProductNumber');
        $masterArticleNumberFieldRole = $this->configHelper->getFieldRole('masterArticleNumber');

        $paramsCollection = [];
        // collect all ordered articles
        /** @var Order\Item $item */
        foreach ($order->getAllVisibleItems() as $item) {
            $product = $item->getProduct();

            $paramsItem = [
                'sid' => $sid,
                'id' => $this->product->get($trackingProductNumberFieldRole, $product, $this->store),
                'masterId' => $this->product->get($masterArticleNumberFieldRole, $product, $this->store),
                'count' => (int) $item->getQtyOrdered(),
                'price' => $this->product->get('Price', $product, $this->store),
            ];

            // build query for current article
            $paramsCollection[] = http_build_query($paramsItem);
        }

        // concatenate get parameters of ordered articles
        $params .= '&' .implode('&', $paramsCollection);

        // track checkout event
        $this->factFinderClient->sendRequest($this->communicationConfig->getAddress() . self::API_NAME, $params);
    }

    /**
     * Get the current tracking session id
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->session->getSessionId();
    }

    /**
     * Get the user id when a user is loged in
     *
     * @return null|string
     */
    public function getUserId()
    {
        return $this->session->isLoggedIn() ? (string) $this->session->getCustomerId() : null;
    }

    /**
     * Get the current used FF channel
     *
     * @return string
     */
    public function getChannel()
    {
        return $this->communicationConfig->getChannel($this->store->getId());
    }
}
