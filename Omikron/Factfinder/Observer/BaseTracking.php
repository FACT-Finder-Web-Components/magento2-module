<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\Data\TrackingProductInterfaceFactory;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Helper\Product as ProductHelper;
use Omikron\Factfinder\Model\Api\Tracking;

abstract class BaseTracking
{
    /** @var Tracking */
    protected $tracking;

    /** @var TrackingProductInterfaceFactory */
    protected $trackingProductFactory;

    /** @var FieldRolesInterface */
    protected $fieldRoles;

    /** @var ProductHelper */
    private $productHelper;

    /** @var StoreManagerInterface */
    private $storeManager;

    public function __construct(
        Tracking $tracking,
        TrackingProductInterfaceFactory $trackingProductFactory,
        ProductHelper $productHelper,
        FieldRolesInterface $fieldRoles,
        StoreManagerInterface $storeManager
    ) {
        $this->tracking               = $tracking;
        $this->trackingProductFactory = $trackingProductFactory;
        $this->productHelper          = $productHelper;
        $this->fieldRoles             = $fieldRoles;
        $this->storeManager           = $storeManager;
    }

    protected function getProductData(string $attribute, Product $product): string
    {
        try {
            $attribute = $this->fieldRoles->getFieldRole($attribute);
            return (string) $this->productHelper->get($attribute, $product, $this->storeManager->getStore());
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }
}
