<?php

declare(strict_types = 1);

namespace Omikron\Factfinder\Model\Consumer;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Helper\Product;
use Omikron\Factfinder\Helper\Data;
use Omikron\Factfinder\Exception\ResponseException;

abstract class AbstractTracking
{
    const EVENT_TRACKING_SUCCESS_RESPONSE = 'The event was successfully tracked';

    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var Session */
    protected $session;

    /** @var ClientInterface */
    protected $factFinderClient;

    /** @var ProductRepositoryInterface */
    protected $productRepository;

    /** @var CommunicationConfigInterface */
    protected $communicationConfig;

    /** @var Product */
    protected $productHelper;

    /** @var Data */
    protected $helper;

    /** @var string */
    protected $apiName = 'Tracking.ff';

    public function __construct(
        Session $session,
        StoreManagerInterface $storeManager,
        ClientInterface $factFinderClient,
        ProductRepositoryInterface $productRepository,
        CommunicationConfigInterface $communicationConfig,
        Product $product,
        Data $data
    ) {
        $this->session             = $session;
        $this->storeManager        = $storeManager;
        $this->factFinderClient    = $factFinderClient;
        $this->productRepository   = $productRepository;
        $this->communicationConfig = $communicationConfig;
        $this->productHelper       = $product;
        $this->helper              = $data;
    }

    protected function trackEvent(string $endpoint, array $params)
    {
        try {
            $this->factFinderClient->sendRequest($endpoint, $params);
        } catch (ResponseException $e) {
            /**
             * Tracking an event returns plaintext which make client thrown an exception.
             * If previous exception was instance of InvalidArgumentException and message is equal
             * to value stored in constant it means that event was pushed successfully
             */
            if ($e->getPrevious() instanceof \InvalidArgumentException && $e->getMessage() == self::EVENT_TRACKING_SUCCESS_RESPONSE) {
                return;
            }
            throw $e;
        }
    }

    protected function getSessionId(): string
    {
        return $this->session->getSessionId();
    }

    protected function getTrackingProductNumber(ProductInterface $product, StoreInterface $store) : string
    {
        return (string) $this->productHelper->get($this->helper->getFieldRole('trackingProductNumber'), $product, $store);
    }

    protected function getMasterArticleNumber(ProductInterface $product, StoreInterface $store) : string
    {
        return (string) $this->productHelper->get($this->helper->getFieldRole('masterArticleNumber'), $product, $store);
    }

    protected function getProductPrice(ProductInterface $product, StoreInterface $store) : string
    {
        return (string) $this->productHelper->get('Price', $product, $store);
    }
}
