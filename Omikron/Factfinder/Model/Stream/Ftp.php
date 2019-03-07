<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Stream;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\Ftp as FtpClient;

class Ftp extends Csv
{
    private const FPT_UPLOAD_CONFIG_PATH = 'factfinder/data_transfer/ff_upload_';

    /** @var FtpClient */
    private $ftpClient;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(
        Filesystem $filesystem,
        FtpClient $ftpClient,
        ScopeConfigInterface $scopeConfig,
        string $filename = 'factfinder/export.csv'
    ) {
        parent::__construct($filesystem, $filename);
        $this->scopeConfig = $scopeConfig;
        $this->ftpClient   = $ftpClient;
    }

    public function __destruct()
    {
        $this->ftpClient->open(
            [
                'host'     => $this->getConfig('host'),
                'user'     => $this->getConfig('user'),
                'password' => $this->getConfig('password'),
                'ssl'      => true,
                'passive'  => true,
                'port'     => 21,
            ]
        );
        $this->ftpClient->write($this->fileName, $this->stream->readAll());
        $this->ftpClient->close();
        parent::__destruct();
    }

    private function getConfig(string $field): string
    {
        return (string) $this->scopeConfig->getValue(self::FPT_UPLOAD_CONFIG_PATH . $field);
    }
}
