<?php

namespace Omikron\Factfinder\Api;

/**
 * Field role provider interface
 *
 * @api
 */
interface FieldRolesInterface
{
    /**
     * Returns all fields used as tracking id
     * @param int|null $scopeId
     * @return string
     */
    public function getFieldRoles(int $scopeId = null): string ;

    /**
     * Returns the specific fields used as tracking id
     *
     * @param string $roleName
     * @param int|null $scopeId
     * @return string
     */
    public function getFieldRole(string $roleName, int $scopeId = null): string;

    /**
     * Store fields in storage
     *
     * @param string $fieldRoles
     * @param int|null $scopeId
     * @return bool
     */
    public function saveFieldRoles(string $fieldRoles, int $scopeId): bool;
}
