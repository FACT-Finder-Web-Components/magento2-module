<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api\Action\Ng;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\Action\PushImportInterface;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\ClientInterfaceFactory;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Model\Api\Credentials;

class PushImport implements PushImportInterface
{
    /** @var ClientInterfaceFactory */
    private $clientFactory;

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var Credentials */
    private $credentials;

    public function __construct(
        ClientInterfaceFactory $clientFactory,
        CommunicationConfigInterface $communicationConfig,
        ScopeConfigInterface $scopeConfig,
        Credentials $credentials
    ) {
        $this->clientFactory       = $clientFactory;
        $this->communicationConfig = $communicationConfig;
        $this->scopeConfig         = $scopeConfig;
        $this->credentials         = $credentials;
    }

    public function execute(int $scopeId = null, array $params = []): bool
    {
        if (!$this->communicationConfig->isPushImportEnabled($scopeId)) {
            return false;
        }

        $params += [
            'channel' => $this->communicationConfig->getChannel($scopeId),
            'quiet'   => 'true',
        ];

        $response = [];
        $endpoint = $this->communicationConfig->getAddress() . '/rest/v3/import';
        /** @var ClientInterface $client */
        $client = $this->clientFactory->create()->setHeaders(['Authorization' => $this->credentials->toBasicAuth()]);
        foreach ($this->getPushImportDataTypes($scopeId) as $type) {
            $response = array_merge_recursive($response, $client->post($endpoint . "/$type", $params));
        }

        return $response && !(isset($response['errors']) || isset($response['error']));
    }

    private function getPushImportDataTypes(int $scopeId = null): array
    {
        $configPath = 'factfinder/data_transfer/ff_push_import_type';
        $dataTypes  = (string) $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $scopeId);

        return $dataTypes ? explode(',', str_replace('data', 'search', $dataTypes)) : [];
    }
}
