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
    private SerializerInterface $serializer;
    private ScopeConfigInterface $scopeConfig;
    private ConfigResource $configResource;
    private SimpleDataProviderFactory $dataProviderFactory;

    /**
     * [sku = string[]]
     */
    private array $dataProviders = [];

    public function __construct(
        SerializerInterface $serializer,
        ScopeConfigInterface $scopeConfig,
        ConfigResource $configResource,
        SimpleDataProviderFactory $dataProviderFactory
    ) {
        $this->serializer          = $serializer;
        $this->scopeConfig         = $scopeConfig;
        $this->configResource      = $configResource;
        $this->dataProviderFactory = $dataProviderFactory;
    }

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

    public function fieldRoleToAttribute(ProductInterface $product, string $roleName): string
    {
        $sku = $product->getSku();
        if (!isset($this->dataProviders[$sku])) {
            $this->dataProviders[$sku] = $this->dataProviderFactory->create(['product' => $product])->toArray();
        }

        return (string) $this->dataProviders[$sku][$this->getFieldRole($roleName)] ?? '';
    }
}
