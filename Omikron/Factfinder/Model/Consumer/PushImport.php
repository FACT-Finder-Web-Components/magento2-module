<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Consumer;

use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Api\ConsumerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class PushImport
{
    /** @var ClientInterface  */
    protected $factFinderClient;

    /** @var CommunicationConfigInterface  */
    protected $communicationConfig;

    /** @var string  */
    protected $apiName = 'Import.ff';

    /** @var ScopeConfigInterface  */
    protected $scopeConfig;

    public function __construct(
        ClientInterface $factFinderClient,
        CommunicationConfigInterface $communicationConfig,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->factFinderClient    = $factFinderClient;
        $this->communicationConfig = $communicationConfig;
        $this->scopeConfig         = $scopeConfig;
    }

    public function execute(int $scopeId = null, array $params = []) : bool
    {
        $channel     = $this->communicationConfig->getChannel($scopeId);
        $importTypes = $this->getPushImportDataTypes($scopeId);
        $endpoint    = $this->communicationConfig->getAddress() . '/' . $this->apiName;
        $response    = [];

        $params = [
                'channel'  => $channel,
                'format'   => 'json',
                'quiet'    => 'true',
                'download' => 'true'
            ] + $params;

        foreach ($importTypes as $type) {
            $params['type'] = $type;
            $response = array_merge_recursive($response, $this->factFinderClient->sendRequest($endpoint, $params));
        }

        if ($responseJson['errors'] ?? $responseJson['error'] ?? []) {
            return false;
        }

        return true;
    }

    private function getPushImportDataTypes(int $scopeId = null) : array
    {
        return explode(',', $this->scopeConfig->getValue('factfinder/data_transfer/ff_push_import_type', ScopeInterface::SCOPE_STORE, $scopeId));
    }
}
