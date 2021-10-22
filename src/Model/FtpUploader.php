<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Framework\Exception\FileSystemException;
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
            //ensure export directory is created
            $this->createExportDirectory();
            $this->client->cd('export');
            // Check write permission
            $this->writeFile($fileName);
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
            $this->createExportDirectory();
            $this->client->cd('export');
            $this->writeFile($filename, $stream->getContent());
        } finally {
            $this->client->close();
        }
    }

    private function trimProtocol(array $config): array
    {
        preg_match('#^(?:s?ftps?)://(.+?)/?$#', $config['host'], $match);

        return $match ? ['host' => $match[1]] + $config : $config;
    }

    private function writeFile(string $fileName, string $content = ''): void
    {
        $result = $this->client->write($fileName, $content);
        if (!$result) {
            throw new FileSystemException(__('Failed to upload file'));
        }
    }

    private function createExportDirectory(): void
    {
        $directoryExist = count(array_filter($this->client->ls(), function (array $entry) {
                return $entry['text'] === self::EXPORT_DIRECTORY_NAME;
            })) > 0;

        if ($directoryExist) {
            return;
        }

        $result = $this->client->mkdir('export', 0777, false);
        if (!$result) {
            throw new FileSystemException(__('Failed to create export directory'));
        }
    }
}
