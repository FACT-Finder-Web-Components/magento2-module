<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Catalog\Model\Product;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Api\SessionDataInterface;
use Omikron\FactFinder\Communication\BuilderInterface;
use Omikron\FactFinder\Communication\Resource\Builder;
use Omikron\FactFinder\Communication\ResourceInterface;
use Omikron\Factfinder\Model\Api\ActionFactory;
use Omikron\Factfinder\Model\Api\CredentialsFactory;
use Psr\Log\LoggerInterface;

abstract class BaseTracking
{
    /** @var FieldRolesInterface */
    protected $fieldRoles;

    /** @var CommunicationConfigInterface */
    protected $communicationConfig;

    /** @var SessionDataInterface */
    protected $sessionData;

    /** @var CredentialsFactory */
    private $credentialsFactory;

    /** @var BuilderInterface */
    private $builder;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        FieldRolesInterface $fieldRoles,
        CommunicationConfigInterface $communicationConfig,
        CredentialsFactory $credentialsFactory,
        SessionDataInterface $sessionData,
        Builder $builder,
        LoggerInterface $logger
    ) {
        $this->fieldRoles          = $fieldRoles;
        $this->communicationConfig = $communicationConfig;
        $this->credentialsFactory  = $credentialsFactory;
        $this->sessionData         = $sessionData;
        $this->builder             = $builder;
        $this->logger              = $logger;
    }

    protected function getProductData(string $roleName, Product $product): string
    {
        return $this->fieldRoles->fieldRoleToAttribute($product, $roleName);
    }

    protected function getTracking(): ResourceInterface
    {
        $this->builder->withServerUrl($this->communicationConfig->getAddress())
            ->withApiVersion($this->communicationConfig->getVersion())
            ->withCredentials($this->credentialsFactory->create());
        if ($this->communicationConfig->isLoggingEnabled()) {
            $this->builder->withLogger($this->logger);
        }

        return $this->builder->build();
    }
}
