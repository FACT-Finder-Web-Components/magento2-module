<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Framework\Filesystem\Io\IoInterface;
use Omikron\Factfinder\Api\StreamInterface;
use Omikron\Factfinder\Model\Config\FtpConfig;

class FtpUploader
{
    /** @var FtpConfig */
    private $config;

    /** @var IoInterface */
    private $client;

    public function __construct(FtpConfig $config, IoInterface $client)
    {
        $this->config = $config;
        $this->client = $client;
    }

    public function testConnection(array $params)
    {
        $this->client->open($params);
        $this->client->write('testconnection', '');
        $this->client->close();
    }

    public function upload(string $filename, StreamInterface $stream): void
    {
        $this->client->open($this->config->toArray());
        $this->client->write($filename, $stream->getContent());
        $this->client->close();
    }
}
