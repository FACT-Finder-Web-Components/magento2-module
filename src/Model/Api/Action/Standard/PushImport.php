<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api\Action\Standard;

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

    /** @var string */
    private $apiName = 'Import.ff';

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
                'channel'  => $this->communicationConfig->getChannel($scopeId),
                'quiet'    => 'true',
                'download' => 'true',
            ] + $this->credentials->toArray();

        $response = [];
        $endpoint = $this->communicationConfig->getAddress() . '/' . $this->apiName;
        /** @var ClientInterface $client */
        $client = $this->clientFactory->create();
        foreach ($this->getPushImportDataTypes($scopeId) as $type) {
            $response = array_merge_recursive($response, $client->get($endpoint, ['type' => $type] + $params));
        }

        return $response && !(isset($response['errors']) || isset($response['error']));
    }

    private function getPushImportDataTypes(int $scopeId = null): array
    {
        $configPath = 'factfinder/data_transfer/ff_push_import_type';
        $dataTypes  = (string) $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $scopeId);

        return $this->toLegacyFormat($dataTypes ? explode(',', $dataTypes) : []);
    }

    private function toLegacyFormat(array $dataTypes): array
    {
        return array_replace($dataTypes, ['data']);
    }
}
