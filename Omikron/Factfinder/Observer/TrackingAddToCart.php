<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Model\Consumer\Tracking;
use Omikron\Factfinder\Api\Data\TrackingProductInterfaceFactory;
use Omikron\Factfinder\Helper\Product;

class TrackingAddToCart implements ObserverInterface
{
    /** @var  Tracking */
    private $tracking;

    /** @var TrackingProductInterfaceFactory  */
    private $trackingProductFactory;

    /** @var Product  */
    private $productHelper;

    /** @var FieldRolesInterface */
    private $fieldRoles;

    /** @var StoreManagerInterface */
    private $storeManager;

    public function __construct(
        Tracking $tracking,
        TrackingProductInterfaceFactory $trackingProductFactory,
        Product $productHelper,
        FieldRolesInterface $fieldRoles,
        StoreManagerInterface $storeManager
    ) {
        $this->tracking               = $tracking;
        $this->trackingProductFactory = $trackingProductFactory;
        $this->productHelper          = $productHelper;
        $this->fieldRoles             = $fieldRoles;
        $this->storeManager           = $storeManager;
    }

    public function execute(Observer $observer)
    {
        $request = $observer->getRequest();
        $product = $observer->getProduct();
        $store   = $this->storeManager->getStore();
        $qty     = (int) $request->getParam('qty') ?? 1;

        $trackingProduct = $this->trackingProductFactory->create([
            'trackingNumber'        => $this->productHelper->get($this->fieldRoles->getFieldRole('trackingProductNumber'), $product, $store),
            'masterArticleNumber'   => $this->productHelper->get($this->fieldRoles->getFieldRole('masterArticleNumber'), $product, $store),
            'price'                 => $product->getFinalPrice($qty),
            'count'                 => $qty
        ]);

        $this->tracking->execute('cart', $trackingProduct);
    }
}
