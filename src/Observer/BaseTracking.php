<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Catalog\Model\Product;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\Data\TrackingProductInterfaceFactory;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Model\Api\Tracking;

abstract class BaseTracking
{
    /** @var Tracking */
    protected $tracking;

    /** @var TrackingProductInterfaceFactory */
    protected $trackingProductFactory;

    /** @var FieldRolesInterface */
    protected $fieldRoles;

    /** @var StoreManagerInterface */
    private $storeManager;

    public function __construct(
        Tracking $tracking,
        TrackingProductInterfaceFactory $trackingProductFactory,
        FieldRolesInterface $fieldRoles,
        StoreManagerInterface $storeManager
    ) {
        $this->tracking               = $tracking;
        $this->trackingProductFactory = $trackingProductFactory;
        $this->fieldRoles             = $fieldRoles;
        $this->storeManager           = $storeManager;
    }

    protected function getProductData(string $roleName, Product $product): string
    {
        return $this->fieldRoles->fieldRoleToAttribute($product, $roleName);
    }
}
