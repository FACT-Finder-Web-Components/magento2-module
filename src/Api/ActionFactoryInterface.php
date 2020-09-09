<?php

declare(strict_types=1);


namespace Omikron\Factfinder\Api;

use Omikron\Factfinder\Api\Action\PushImportInterface;
use Omikron\Factfinder\Api\Action\TestConnectionInterface;
use Omikron\Factfinder\Api\Action\TrackingInterface;
use Omikron\Factfinder\Api\Action\UpdateFieldRolesInterface;
use Omikron\Factfinder\Model\Api\Credentials;

/**
 * @api
 */
interface ActionFactoryInterface
{
    /**
     * Set credentials to the resource to be build in a runtime.
     * If not used, credentials stored in config will be used
     *
     * @param Credentials $credentials
     *
     * @return ActionFactoryInterface
     */
    public function withCredentials(Credentials $credentials): ActionFactoryInterface;

    /**
     * Set version for which the resources have to be build.
     *
     * @param string $apiVersion
     *
     * @return ActionFactoryInterface
     */
    public function withApiVersion(string $apiVersion): ActionFactoryInterface;

    public function getTestConnection(): TestConnectionInterface;

    public function getTracking(): TrackingInterface;

    public function getPushImport(): PushImportInterface;

    public function getUpdateFieldRoles(): UpdateFieldRolesInterface;
}
