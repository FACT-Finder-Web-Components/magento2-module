<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

class FtpConfig
{
    private const FPT_UPLOAD_CONFIG_PATH = 'factfinder/data_transfer/ff_upload_';
    private const UPLOAD_TYPE_SFTP = 'sftp';
    private const AUTHENTICATION_TYPE_KEY = 'key';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function toArray(): array
    {
        return [
            'host'           => $this->getConfig('host'),
            'user'           => $this->getConfig('user'),
            'username'       => $this->getConfig('user'), // adjustments for \Magento\Framework\Filesystem\Io\Sftp
            'password'       => $this->getConfig('password'),
            'ssl'            => (bool) $this->getConfig('use_ssl'),
            'passive'        => true,
            'port'           => $this->getConfig('port') ?: 21,
            'key_passphrase' => $this->getConfig('key_passphrase'),
        ];
    }

    private function getConfig(string $field): string
    {
        return (string) $this->scopeConfig->getValue(self::FPT_UPLOAD_CONFIG_PATH . $field);
    }

    public function isPublicKeyAuthentication(): bool
    {
        return $this->getConfig('authentication_type') === self::AUTHENTICATION_TYPE_KEY;
    }

    public function getUploadDirectory(): string
    {
        return (string) $this->getConfig('dir');
    }

    public function getKeyFileName(): string
    {
        return 'factfinder/sftp/' . $this->getConfig('authentication_key');
    }

    public function getUserPassword(): string
    {
        return $this->getConfig('password');
    }

    public function getKeyPassphrase(): string
    {
        return $this->getConfig('key_passphrase');
    }
}
