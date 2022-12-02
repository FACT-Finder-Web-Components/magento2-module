<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface as Scope;
use Omikron\Factfinder\Model\Export\Catalog\ProductType\SimpleDataProviderFactory;

class FieldRoles
{
    private const PATH_PRODUCT_FIELD_ROLE = 'factfinder/general/tracking_product_number_field_role';

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly ConfigResource $configResource,
        private readonly SimpleDataProviderFactory $dataProviderFactory
    ) {}

    public function getFieldRoles(int $scopeId = null): array
    {
        try {
            $config = $this->scopeConfig->getValue(self::PATH_PRODUCT_FIELD_ROLE, Scope::SCOPE_STORES, $scopeId);
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
            $this->configResource->saveConfig(self::PATH_PRODUCT_FIELD_ROLE, $roles, Scope::SCOPE_STORES, $scopeId);
            return true;
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }
}
