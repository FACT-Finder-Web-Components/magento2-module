<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Catalog\Model\Product;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
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

    /** @var CommunicationConfigInterface  */
    protected $config;

    public function __construct(
        Tracking $tracking,
        TrackingProductInterfaceFactory $trackingProductFactory,
        FieldRolesInterface $fieldRoles,
        CommunicationConfigInterface $config
    ) {
        $this->tracking               = $tracking;
        $this->trackingProductFactory = $trackingProductFactory;
        $this->fieldRoles             = $fieldRoles;
        $this->config                 = $config;
    }

    protected function getProductData(string $roleName, Product $product): string
    {
        return $this->fieldRoles->fieldRoleToAttribute($product, $roleName);
    }
}
