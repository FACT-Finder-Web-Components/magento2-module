<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api;

use InvalidArgumentException;
use Magento\Framework\ObjectManagerInterface;
use Omikron\Factfinder\Api\Action\PushImportInterface;
use Omikron\Factfinder\Api\Action\TestConnectionInterface;
use Omikron\Factfinder\Api\Action\TrackingInterface;
use Omikron\Factfinder\Api\Action\UpdateFieldRolesInterface;
use Omikron\Factfinder\Api\ActionFactoryInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;

class ActionFactory implements ActionFactoryInterface
{
    /** @var ObjectManagerInterface */
    private $objectManager;

    /** @var string[] */
    private $resourcePool;

    /** @var Credentials */
    private $credentials;

    /** @var string */
    private $apiVersion;

    /** @var CredentialsFactory */
    private $credentialsFactory;

    public function __construct(
        ObjectManagerInterface $objectManager,
        CredentialsFactory $credentialsFactory,
        array $resourcePool = []
    ) {
        $this->objectManager      = $objectManager;
        $this->credentialsFactory = $credentialsFactory;
        $this->resourcePool       = $resourcePool;
    }

    /**
     * {@inheritDoc}
     */
    public function withCredentials(Credentials $credentials): ActionFactoryInterface
    {
        $this->credentials = $credentials;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function withApiVersion(string $apiVersion): ActionFactoryInterface
    {
        $this->apiVersion = $apiVersion === CommunicationConfigInterface::NG_VERSION ? $apiVersion : 'standard';

        return $this;
    }

    public function getTestConnection(): TestConnectionInterface
    {
        return $this->getResource('testConnection');
    }

    public function getTracking(): TrackingInterface
    {
        return $this->getResource('tracking');
    }

    public function getPushImport(): PushImportInterface
    {
        return $this->getResource('pushImport');
    }

    public function getUpdateFieldRoles(): UpdateFieldRolesInterface
    {
        return $this->getResource('updateFieldRoles');
    }

    private function getResource(string $type)
    {
        if (!isset($this->resourcePool[$this->apiVersion][$type])) {
            throw new InvalidArgumentException(
                sprintf('There is no resource with a given type: %s for used FACT-Finder version', $type)
            );
        }

        return $this->objectManager->create(
            $this->resourcePool[$this->apiVersion][$type],
            ['credentials' => $this->credentials ?? $this->credentialsFactory->create()]
        );
    }
}
