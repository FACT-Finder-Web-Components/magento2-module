<?php

declare(strict_types = 1);

namespace Omikron\Factfinder\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;

class CommunicationConfig implements CommunicationConfigInterface
{
    const PATH_CHANNEL                    = 'factfinder/general/channel';
    const PATH_ADDRESS                    = 'factfinder/general/address';
    const PATH_DATA_TRANSFER_IMPORT       = 'factfinder/data_transfer/ff_push_import_enabled';
    const PATH_DATA_TRANSFER_IMPORT_TYPES = 'factfinder/data_transfer/ff_push_import_type';

    /** @var ScopeConfigInterface  */
    protected $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getChannel(int $scopeId = null) : string
    {
        return (string) $this->scopeConfig->getValue(self::PATH_CHANNEL, ScopeInterface::SCOPE_STORES, $scopeId);
    }

    public function getAddress() : string
    {
       return (string) $this->scopeConfig->getValue(self::PATH_ADDRESS);
    }

    public function getPushImportTypes(string $scopeId = null) : array
    {
        return explode(',', $this->scopeConfig->getValue(self::PATH_DATA_TRANSFER_IMPORT_TYPES, ScopeInterface::SCOPE_STORES, $scopeId));
    }
}
