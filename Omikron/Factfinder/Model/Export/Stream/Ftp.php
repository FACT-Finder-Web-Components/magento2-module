<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Stream;

use Omikron\Factfinder\Api\Export\StreamInterface;
use Magento\Framework\Filesystem\Io\Ftp as FtpClient;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Ftp extends StreamDecorator
{
    private const FPT_UPLOAD_CONFIG_PATH = 'factfinder/data_transfer/ff_upload_';

    /** @var FtpClient */
    private $ftpClient;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var string */
    private $fileName;

    /** @var bool */
    private $isOpen = false;

    public function __construct(
        StreamInterface $decoratedStream,
        FtpClient $ftpClient,
        ScopeConfigInterface $scopeConfig,
        string $fileName = 'export.csv'
    ) {
        parent::__construct($decoratedStream);
        $this->ftpClient   = $ftpClient;
        $this->scopeConfig = $scopeConfig;
        $this->fileName    = $fileName;
    }

    public function addEntity(array $entity): void
    {
        if (!$this->isOpen) {
            $this->openConnection();
        }

        $result = $this->ftpClient->write($this->fileName, $this->toCsvLine($entity));
        if (!$result) {
            throw new \InvalidArgumentException('Unable to save entity to remote file');
        }
        $this->decoratedStream->addEntity($entity);
    }

    public function __destruct()
    {
        $this->ftpClient->close();
    }

    private function openConnection()
    {
        $this->isOpen = $this->ftpClient->open(
            [
                'host'     => $this->getConfig('host'),
                'user'     => $this->getConfig('user'),
                'password' => $this->getConfig('password'),
                'ssl'      => true,
                'passive'  => true,
                'port'     => 21,
            ]
        );
    }

    private function getConfig(string $field): string
    {
        return (string) $this->scopeConfig->getValue(self::FPT_UPLOAD_CONFIG_PATH . $field);
    }

    private function toCsvLine(array $data): string
    {
        return implode(';', $data) . PHP_EOL;
    }
}
