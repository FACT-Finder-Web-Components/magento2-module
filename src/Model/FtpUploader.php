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

    /**
     * @param array $params
     * @throws \Exception When credentials are invalid or target directory is not writeable
     */
    public function testConnection(array $params): void
    {
        $fileName = 'testconnection';
        try {
            $this->client->open($this->trimProtocol($params));
            //check write permission
            $this->client->write($fileName, '');
            $this->client->rm($fileName);
        } finally {
            $this->client->close();
        }
    }

    /**
     * @param string          $filename
     * @param StreamInterface $stream
     * @throws \Exception
     */
    public function upload(string $filename, StreamInterface $stream): void
    {
        try {
            $this->client->open($this->trimProtocol($this->config->toArray()));
            $this->client->write($filename, $stream->getContent());
        } finally {
            $this->client->close();
        }
    }

    private function trimProtocol(array $config): array
    {
        $config['host'] = preg_replace('#^(ftp|sftp|ftps)?://#', '', $config['host']);
        return $config;
    }
}
