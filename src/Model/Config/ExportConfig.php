<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\FactFinder\Communication\Version;

class ExportConfig
{
    private const CONFIG_PATH = 'factfinder/export/attributes';

    private ScopeConfigInterface $scopeConfig;
    private SerializerInterface $serializer;
    private CommunicationConfig $communicationConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer,
        CommunicationConfig $communicationConfig
    ) {
        $this->scopeConfig         = $scopeConfig;
        $this->serializer          = $serializer;
        $this->communicationConfig = $communicationConfig;
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getMultiAttributes(?int $storeId = null, bool $numerical = false): array
    {
        return $this->getAttributeCodes($storeId, fn (array $row): bool => $row['multi'] && (bool) $row['numerical'] == $numerical);
    }

    public function getSingleFields(?int $storeId = null): array
    {
        return $this->getAttributeCodes($storeId, fn (array $row): bool => !$row['multi']);
    }

    public function getPushImportDataTypes(int $scopeId = null): array
    {
        $configPath = 'factfinder/data_transfer/ff_push_import_type';
        $dataTypes  = (string) $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORES, $scopeId);
        $isNg       = $this->communicationConfig->getVersion() === Version::NG;

        return explode(',', $isNg ? $dataTypes : str_replace('search', 'data', $dataTypes));
    }

    private function getAttributeCodes(?int $storeId, callable $condition): array
    {
        $rows = array_filter($this->getConfigValue($storeId), $condition);
        return array_values(array_unique(array_column($rows, 'code')));
    }

    private function getConfigValue(?int $storeId): array
    {
        $value = $this->scopeConfig->getValue(self::CONFIG_PATH, ScopeInterface::SCOPE_STORES, $storeId);
        return array_map(
            fn (array $row): array => ['multi' => !!$row['multi']] + $row,
            (array) $this->serializer->unserialize($value ?: '[]')
        );
    }
}
