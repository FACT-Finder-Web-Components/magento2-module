<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Model\Consumer\Tracking;
use Omikron\Factfinder\Api\Data\TrackingProductInterfaceFactory;
use Omikron\Factfinder\Helper\Product;

abstract class BaseTracking
{
    /** @var  Tracking */
    protected $tracking;

    /** @var TrackingProductInterfaceFactory  */
    protected $trackingProductFactory;

    /** @var Product  */
    protected $productHelper;

    /** @var FieldRolesInterface */
    protected $fieldRoles;

    /** @var StoreManagerInterface */
    protected $storeManager;

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
}
