<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Model\Consumer\Tracking;
use Omikron\Factfinder\Api\Data\TrackingProductInterfaceFactory;
use Omikron\Factfinder\Helper\Product;

class TrackingCheckout implements ObserverInterface
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
        /** @var Quote $cart */
        $cart = $observer->getEvent()->getData('quote');
        $store = $this->storeManager->getStore();

        $trackingProducts = array_map(function (Item $item) use ($store) {
            return $this->trackingProductFactory->create(
                [
                    'trackingNumber'        => $this->productHelper->get($this->fieldRoles->getFieldRole('trackingProductNumber'), $item->getProduct(), $store),
                    'masterArticleNumber'   => $this->productHelper->get($this->fieldRoles->getFieldRole('masterArticleNumber'), $item->getProduct(), $store),
                    'price'                 => $item->getPrice(),
                    'count'                 => $item->getQty()
                ]
            );
        }, $cart->getAllVisibleItems());

        $this->tracking->execute('checkout', ...$trackingProducts);
    }
}
