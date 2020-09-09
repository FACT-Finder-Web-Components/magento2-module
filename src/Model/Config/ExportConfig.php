<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;

class ExportConfig
{
    private const CONFIG_PATH = 'factfinder/export/attributes';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(ScopeConfigInterface $scopeConfig, SerializerInterface $serializer)
    {
        $this->scopeConfig = $scopeConfig;
        $this->serializer  = $serializer;
    }

    public function getMultiAttributes(?int $storeId = null): array
    {
        return $this->getAttributeCodes($storeId, function (array $row): bool {
            return $row['multi'];
        });
    }

    public function getSingleFields(?int $storeId = null): array
    {
        return $this->getAttributeCodes($storeId, function (array $row): bool {
            return !$row['multi'];
        });
    }

    private function getAttributeCodes(?int $storeId, callable $condition): array
    {
        $rows = array_filter($this->getConfigValue($storeId), $condition);
        return array_values(array_unique(array_column($rows, 'code')));
    }

    private function getConfigValue(?int $storeId): array
    {
        $value = $this->scopeConfig->getValue(self::CONFIG_PATH, ScopeInterface::SCOPE_STORES, $storeId);
        return array_map(function (array $row): array {
            return ['multi' => !!$row['multi']] + $row;
        }, (array) $this->serializer->unserialize($value ?: '[]'));
    }
}
