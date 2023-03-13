<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Filesystem\Io;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\Sftp as SftpBase;
use Omikron\Factfinder\Model\Config\FtpConfig;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\Common\PrivateKey;
use phpseclib3\Net\SFTP;

class SftpPublicKeyAuth extends SftpBase
{
    /**
     * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
     * phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine
     */
    public function __construct(
        private readonly Filesystem $fileSystem,
        private readonly FtpConfig $config,
    ) {}

    public function open(array $args = [])
    {
        $this->_connection = new SFTP($args['host'], $args['port'], self::REMOTE_TIMEOUT);
        if (!$this->_connection->login($args['user'], $this->getKey($args['key_passphrase']))) {
            throw new Exception(sprintf('Unable to open SFTP connection as %s@%s', $args['user'], $args['host']));
        }
    }

    protected function getKey(string $passphrase): PrivateKey
    {
        $configDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::CONFIG);
        $filesInLocation = $configDirectory->read('factfinder/sftp');
        $keyFile         = $configDirectory->readFile($filesInLocation[$this->getFileIndex($filesInLocation)]);
        $privateKey      = PublicKeyLoader::loadPrivateKey($keyFile);

        if ($passphrase) {
            $privateKey = PublicKeyLoader::loadPrivateKey($keyFile, $passphrase);
        }

        return $privateKey;
    }

    /**
     * @return int
     * @throws FileSystemException
     *
     */
    private function getFileIndex(array $directoryContent): int
    {
        $index = array_search($this->uploadConfig->getKeyFileName(), $directoryContent);

        if ($index === false) {
            throw new FileSystemException(__('The key file does not exist'));
        }

        return $index;
    }
}
