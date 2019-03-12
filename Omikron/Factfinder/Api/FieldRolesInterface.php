<?php

namespace Omikron\Factfinder\Api;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Field role provider interface
 *
 * @api
 */
interface FieldRolesInterface
{
    /**
     * Returns all fields used as tracking id
     *
     * @param int|null $scopeId
     *
     * @return array
     */
    public function getFieldRoles(int $scopeId = null): array;

    /**
     * Returns the specific fields used as tracking id
     *
     * @param string   $roleName
     * @param int|null $scopeId
     *
     * @return string
     */
    public function getFieldRole(string $roleName, int $scopeId = null): string;

    /**
     * Store fields in storage
     *
     * @param array $fieldRoles
     * @param null|int   $scopeId
     *
     * @return bool
     */
    public function saveFieldRoles(array $fieldRoles, int $scopeId = null): bool;

    /**
     * Gets mapped from Field Roles product attribute value
     *
     * @param ProductInterface $product
     * @param string           $roleName
     *
     * @return string
     */
    public function fieldRoleToAttribute(ProductInterface $product, string $roleName): string;
}
