<?php

namespace Omikron\Factfinder\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use Magento\Sales\Model\Order;

/**
 * Class Tracking
 * Helper Class for sending tracking events to FF
 *
 * @package Omikron\Factfinder\Helper
 */
class Tracking extends AbstractHelper
{
    const API_NAME = "Tracking.ff";
    const EVENT_TYPE_CART = "cart";
    const EVENT_TYPE_CHECKOUT = "checkout";

    /** @var Communication */
    protected $_communication;

    /** @var Product */
    protected $_product;

    /** @var \Magento\Store\Model\Store */
    protected $_store;

    /** @var \Magento\Customer\Model\Session */
    protected $_session;

    /** @var Data */
    protected $_helper;

    /**
     * Tracking constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param Communication $communication
     * @param Product $product
     * @param \Magento\Store\Model\Store $store
     * @param \Magento\Customer\Model\Session $session
     * @param Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Communication $communication,
        Product $product,
        \Magento\Store\Model\Store $store,
        \Magento\Customer\Model\Session $session,
        Data $helper
    )
    {
        $this->_communication = $communication;
        $this->_product = $product;
        $this->_session = $session;
        $this->_helper = $helper;
        $this->_store = $store;

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
            'id' => $this->_product->get($this->_helper->getFieldRole('trackingProductNumber'), $product, $this->_store),
            'masterId' => $this->_product->get($this->_helper->getFieldRole('masterArticleNumber'), $product, $this->_store),
            'price' => $this->_product->get('Price', $product, $this->_store),
            'count' => (int) round($amount),
            'sid' => $this->getSessionId(),
            'channel' => $this->getChannel(),
        ];

        if (!is_null($userId = $this->getUserId())) {
            $params['userId'] = $userId;
        }

        // track cart event
        $this->_communication->sendToFF(self::API_NAME, $params);
    }

    /**
     * Sends tracking events for checkout to FF
     *
     * @param $order Order
     */
    public function checkout($order)
    {
        $baseParams = [
            "event" => self::EVENT_TYPE_CHECKOUT,
            "channel" => $this->getChannel(),
        ];

        // start with mandatory get parameters
        $params = http_build_query($baseParams);

        $sid = $this->getSessionId();
        $trackingProductNumberFieldRole = $this->_helper->getFieldRole('trackingProductNumber');
        $masterArticleNumberFieldRole = $this->_helper->getFieldRole('masterArticleNumber');

        $paramsCollection = [];
        // collect all ordered articles
        /** @var Order\Item $item */
        foreach ($order->getAllVisibleItems() as $item) {
            $product = $item->getProduct();

            $paramsItem = [
                "sid" => $sid,
                "id" => $this->_product->get($trackingProductNumberFieldRole, $product, $this->_store),
                'masterId' => $this->_product->get($masterArticleNumberFieldRole, $product, $this->_store),
                "count" => (int)$item->getQtyOrdered(),
                "price" => $this->_product->get("Price", $product, $this->_store),
            ];

            // build query for current article
            $paramsCollection[] = http_build_query($paramsItem);
        }

        // concatenate get parameters of ordered articles
        $params .= implode("&", $paramsCollection);

        // track checkout event
        $this->_communication->sendToFF(self::API_NAME, $params);
    }

    /**
     * Get the current tracking session id
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->_session->getSessionId();
    }

    /**
     * Get the user id when a user is loged in
     *
     * @return null|string
     */
    public function getUserId()
    {
        return $this->_session->isLoggedIn() ? (string)$this->_session->getCustomerId() : null;
    }

    /**
     * Get the current used FF channel
     *
     * @return string
     */
    public function getChannel()
    {
        return $this->_helper->getChannel();
    }
}
