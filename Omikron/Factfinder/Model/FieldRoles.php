<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface as Scope;
use Omikron\Factfinder\Api\FieldRolesInterface;

class FieldRoles implements FieldRolesInterface
{
    const PATH_PRODUCT_FIELD_ROLE = 'factfinder/general/tracking_product_number_field_role';

    /** @var SerializerInterface */
    private $serializer;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var ConfigResource */
    private $configResource;

    public function __construct(
        SerializerInterface $serializer,
        ScopeConfigInterface $scopeConfig,
        ConfigResource $configResource
    ) {
        $this->serializer     = $serializer;
        $this->scopeConfig    = $scopeConfig;
        $this->configResource = $configResource;
    }

    public function getFieldRoles(int $scopeId = null): array
    {
        try {
            $config = $this->scopeConfig->getValue(self::PATH_PRODUCT_FIELD_ROLE, Scope::SCOPE_STORE, $scopeId);
            return (array) $this->serializer->unserialize($config);
        } catch (\InvalidArgumentException $e) {
            return [];
        }
    }

    public function getFieldRole(string $roleName, int $scopeId = null): string
    {
        return (string) ($this->getFieldRoles($scopeId)[$roleName] ?? '');
    }

    public function saveFieldRoles(array $fieldRoles, int $scopeId = null): bool
    {
        try {
            $roles = (string) $this->serializer->serialize($fieldRoles);
            $this->configResource->saveConfig(self::PATH_PRODUCT_FIELD_ROLE, $roles, Scope::SCOPE_STORE, $scopeId);
            return true;
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }
}
