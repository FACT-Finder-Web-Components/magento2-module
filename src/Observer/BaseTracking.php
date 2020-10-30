<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Catalog\Model\Product;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Api\Data\TrackingProductInterfaceFactory;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Api\SessionDataInterface;
use Omikron\FactFinder\Communication\Resource\Builder;
use Omikron\FactFinder\Communication\ResourceInterface;
use Omikron\Factfinder\Model\Api\ActionFactory;
use Omikron\Factfinder\Model\Api\CredentialsFactory;

abstract class BaseTracking
{
    /** @var ActionFactory */
    protected $actionFactory;

    /** @var TrackingProductInterfaceFactory */
    protected $trackingProductFactory;

    /** @var FieldRolesInterface */
    protected $fieldRoles;

    /** @var CommunicationConfigInterface */
    protected $communicationConfig;

    /** @var SessionDataInterface */
    protected $sessionData;

    /** @var CredentialsFactory */
    private $credentialsFactory;

    public function __construct(
        ActionFactory $actionFactory,
        TrackingProductInterfaceFactory $trackingProductFactory,
        FieldRolesInterface $fieldRoles,
        CommunicationConfigInterface $communicationConfig,
        CredentialsFactory $credentialsFactory,
        SessionDataInterface $sessionData
    ) {
        $this->actionFactory          = $actionFactory;
        $this->trackingProductFactory = $trackingProductFactory;
        $this->fieldRoles             = $fieldRoles;
        $this->communicationConfig    = $communicationConfig;
        $this->credentialsFactory     = $credentialsFactory;
        $this->sessionData            = $sessionData;
    }

    protected function getProductData(string $roleName, Product $product): string
    {
        return $this->fieldRoles->fieldRoleToAttribute($product, $roleName);
    }

    protected function getTracking(): ResourceInterface
    {
        return (new Builder())
            ->withServerUrl($this->communicationConfig->getAddress())
            ->withApiVersion($this->communicationConfig->getVersion())
            ->withCredentials($this->credentialsFactory->create())
            ->build();
    }
}
