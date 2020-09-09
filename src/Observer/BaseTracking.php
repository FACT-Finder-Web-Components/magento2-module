<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Catalog\Model\Product;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Api\Data\TrackingProductInterfaceFactory;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Api\Action\TrackingInterface;
use Omikron\Factfinder\Model\Api\ActionFactory;

abstract class BaseTracking
{
    /** @var ActionFactory */
    protected $actionFactory;

    /** @var TrackingProductInterfaceFactory */
    protected $trackingProductFactory;

    /** @var FieldRolesInterface */
    protected $fieldRoles;

    /** @var CommunicationConfigInterface */
    protected $config;

    public function __construct(
        ActionFactory $actionFactory,
        TrackingProductInterfaceFactory $trackingProductFactory,
        FieldRolesInterface $fieldRoles,
        CommunicationConfigInterface $config
    ) {
        $this->actionFactory        = $actionFactory;
        $this->trackingProductFactory = $trackingProductFactory;
        $this->fieldRoles             = $fieldRoles;
        $this->config                 = $config;
    }

    protected function getProductData(string $roleName, Product $product): string
    {
        return $this->fieldRoles->fieldRoleToAttribute($product, $roleName);
    }

    protected function getTracking(): TrackingInterface
    {
        return $this->actionFactory->withApiVersion($this->config->getVersion())->getTracking();
    }
}
