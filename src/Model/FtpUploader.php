<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Framework\Filesystem\Io\IoInterface;
use Omikron\Factfinder\Api\StreamInterface;
use Omikron\Factfinder\Model\Config\FtpConfig;
use Omikron\Factfinder\Model\Filesystem\Io\Factory as UploadFactory;

class FtpUploader
{
    /** @var FtpConfig */
    private $config;

    /** @var UploadFactory */
    private $uploadFactory;

    /** @var IoInterface */
    private $client;

    public function __construct(FtpConfig $config, UploadFactory $uploadFactory)
    {
        $this->config        = $config;
        $this->uploadFactory = $uploadFactory;
    }

    /**
     * @param array $params
     *
     * @throws \Exception When credentials are invalid or target directory is not writeable
     */
    public function testConnection(array $params): void
    {
        $fileName = 'testconnection';
        try {
            $this->client = $this->uploadFactory->create($params);
            $this->client->open($this->trimProtocol($params));
            $this->client->cd('export');
            // Check write permission
            $this->client->write($fileName, '');
            $this->client->rm($fileName);
        } finally {
            $this->client->close();
        }
    }

    /**
     * @param string          $filename
     * @param StreamInterface $stream
     *
     * @throws \Exception
     */
    public function upload(string $filename, StreamInterface $stream): void
    {
        try {
            $this->client = $this->uploadFactory->create();

            $this->client->open($this->trimProtocol($this->config->toArray()));
            $this->client->cd('export');
            $this->client->write($filename, $stream->getContent());
        } finally {
            $this->client->close();
        }
    }

    private function trimProtocol(array $config): array
    {
        preg_match('#^(?:s?ftps?)://(.+?)/?$#', $config['host'], $match);

        return $match ? ['host' => $match[1]] + $config : $config;
    }
}
