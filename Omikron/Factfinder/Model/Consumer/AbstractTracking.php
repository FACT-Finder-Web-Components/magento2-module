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

abstract class AbstractTracking
{
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
