<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface as Scope;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Model\Export\Catalog\ProductType\SimpleDataProviderFactory;

class FieldRoles implements FieldRolesInterface
{
    private const PATH_PRODUCT_FIELD_ROLE = 'factfinder/general/tracking_product_number_field_role';

    /** @var SerializerInterface */
    private $serializer;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var ConfigResource */
    private $configResource;

    /** @var SimpleDataProviderFactory */
    private $dataProviderFactory;

    /** @var array */
    private $dataProviders = [];

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
            $scope  = $scopeId ? Scope::SCOPE_STORE : ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
            $config = $this->scopeConfig->getValue(self::PATH_PRODUCT_FIELD_ROLE, $scope, $scopeId);
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
            $scope = $scopeId ? Scope::SCOPE_STORE : ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
            $this->configResource->saveConfig(self::PATH_PRODUCT_FIELD_ROLE, $roles, $scope, $scopeId);
            return true;
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    public function fieldRoleToAttribute(ProductInterface $product, string $roleName): string
    {
        $attribute = $this->getFieldRole($roleName);
        if (!isset($this->dataProviders[$product->getSku()])) {
            $this->dataProviders[$product->getSku()] = $this->dataProviderFactory->create(['product' => $product])->toArray();
        }

        return (string) $this->dataProviders[$product->getSku()][$attribute] ?? '';
    }
}
