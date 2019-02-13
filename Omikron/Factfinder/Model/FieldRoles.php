<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\FieldRolesInterface;

class FieldRoles implements FieldRolesInterface
{
    const PATH_PRODUCT_FIELD_ROLE = 'factfinder/general/tracking_product_number_field_role';

    /** @var SerializerInterface  */
    private $serializer;

    /** @var ScopeConfigInterface  */
    private $scopeConfig;

    /** @var ConfigResource  */
    private $configResource;

    public function __construct(
        SerializerInterface $serializer,
        ScopeConfigInterface $scopeConfig,
        ConfigResource $configResource
    ) {
        $this->serializer     = $serializer;
        $this->scopeConfig     = $scopeConfig;
        $this->configResource = $configResource;
    }

    public function getFieldRoles(int $scopeId = null): string
    {
        return $this->scopeConfig->getValue(self::PATH_PRODUCT_FIELD_ROLE, 'store');
    }

    /**
     * @param string   $roleName
     * @param int|null $scopeId
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getFieldRole(string $roleName, int $scopeId = null): string
    {
        $roles = $this->serializer->unserialize($this->getFieldRoles(), true);
        return $roles[$roleName] ?? '';
    }

    public function saveFieldRoles(string $fieldRoles, int $scopeId = null): bool
    {
        try {
            $this->serializer->unserialize($fieldRoles);
            $this->configResource->saveConfig(self::PATH_PRODUCT_FIELD_ROLE, $fieldRoles, ScopeInterface::SCOPE_STORES, $scopeId);

            return true;
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }
}
