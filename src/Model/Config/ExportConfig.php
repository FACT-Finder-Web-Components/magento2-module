<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;

class ExportConfig
{
    private const CONFIG_PATH = 'factfinder/export/attributes';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var SerializerInterface */
    private $serializer;

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer,
        CommunicationConfigInterface $communicationConfig
    ) {
        $this->scopeConfig         = $scopeConfig;
        $this->serializer          = $serializer;
        $this->communicationConfig = $communicationConfig;
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

    public function getPushImportDataTypes(int $scopeId = null): array
    {
        $configPath = 'factfinder/data_transfer/ff_push_import_type';
        $dataTypes  = (string) $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $scopeId);
        $isNg       = $this->communicationConfig->getVersion() !== CommunicationConfigInterface::NG_VERSION;

        return explode(',', $isNg ? str_replace('search', 'data', $dataTypes) : $dataTypes);
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
