<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

class FtpConfig
{
    private const FPT_UPLOAD_CONFIG_PATH = 'factfinder/data_transfer/ff_upload_';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function toArray(): array
    {
        return [
            'host'     => $this->getConfig('host'),
            'user'     => $this->getConfig('user'),
            'password' => $this->getConfig('password'),
            'ssl'      => (bool) $this->getConfig('use_ssl'),
            'passive'  => true,
            'port'     => $this->getConfig('port') ?: 21,
        ];
    }

    private function getConfig(string $field): string
    {
        return (string) $this->scopeConfig->getValue(self::FPT_UPLOAD_CONFIG_PATH . $field);
    }
}
