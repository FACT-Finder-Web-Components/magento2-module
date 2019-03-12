<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Stream;

use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\Ftp as FtpClient;
use Omikron\Factfinder\Model\Config\FtpConfig;

class Ftp extends Csv
{
    /** @var FtpClient */
    private $client;

    /** @var FtpConfig */
    private $config;

    public function __construct(
        Filesystem $filesystem,
        FtpClient $client,
        FtpConfig $config,
        string $filename = 'factfinder/export.csv'
    ) {
        parent::__construct($filesystem, $filename);
        $this->config = $config;
        $this->client = $client;
    }

    public function dispose(): bool
    {
        $this->client->open($this->config->toArray());
        $this->client->write(basename($this->filename), $this->getContent());
        $this->client->close();
        return parent::dispose();
    }
}
