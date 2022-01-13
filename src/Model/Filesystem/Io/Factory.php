<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Filesystem\Io;

use Magento\Framework\Filesystem\Io\Ftp;
use Magento\Framework\Filesystem\Io\IoInterface;
use Magento\Framework\Filesystem\Io\Sftp;
use Magento\Framework\ObjectManagerInterface;
use Omikron\Factfinder\Model\Config\FtpConfig;

class Factory
{
    private ObjectManagerInterface $objectManager;
    private FtpConfig $uploadConfig;

    public function __construct(FtpConfig $uploadConfig, ObjectManagerInterface $objectManager)
    {
        $this->uploadConfig  = $uploadConfig;
        $this->objectManager = $objectManager;
    }

    public function create(array $params): IoInterface
    {
        $type =  Ftp::class;
        if ($params['type'] === 'sftp') {
            $type = $params['authentication_type'] === 'key' ? SftpPublicKeyAuth::class : Sftp::class;
        }
        return $this->objectManager->create($type);
    }
}
