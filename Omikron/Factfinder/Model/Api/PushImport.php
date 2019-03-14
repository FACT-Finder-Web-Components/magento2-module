<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;

class PushImport
{
    /** @var ClientInterface */
    protected $apiClient;

    /** @var CommunicationConfigInterface */
    protected $communicationConfig;

    /** @var string */
    protected $apiName = 'Import.ff';

    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    public function __construct(
        ClientInterface $apiClient,
        CommunicationConfigInterface $communicationConfig,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->apiClient           = $apiClient;
        $this->communicationConfig = $communicationConfig;
        $this->scopeConfig         = $scopeConfig;
    }

    public function execute(int $scopeId = null, array $params = []): bool
    {
        if (!$this->communicationConfig->isPushImportEnabled($scopeId)) {
            return false;
        }
        $endpoint = $this->communicationConfig->getAddress() . '/' . $this->apiName;

        $params += [
            'channel'  => $this->communicationConfig->getChannel($scopeId),
            'quiet'    => 'true',
            'download' => 'true',
        ];

        $response = [];
        foreach ($this->getPushImportDataTypes($scopeId) as $type) {
            $params['type'] = $type;
            $response       = array_merge_recursive($response, $this->apiClient->sendRequest($endpoint, $params));
        }

        return $response && !(isset($response['errors']) || isset($response['error']));
    }

    private function getPushImportDataTypes(int $scopeId = null): array
    {
        $configPath = 'factfinder/data_transfer/ff_push_import_type';
        $dataTypes  = (string) $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $scopeId);
        return $dataTypes ? explode(',', $dataTypes) : [];
    }
}
