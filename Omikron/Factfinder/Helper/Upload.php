<?php

namespace Omikron\Factfinder\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem\Io\Ftp;
use Magento\Store\Model\ScopeInterface;

class Upload extends AbstractHelper
{
    private const CONFIG_PATH = 'factfinder/data_transfer/ff_upload_';

    /** @var Ftp */
    private $ftp;

    /** @var DirectoryList */
    private $directoryList;

    public function __construct(Context $context, Ftp $ftp, DirectoryList $directoryList)
    {
        parent::__construct($context);
        $this->ftp           = $ftp;
        $this->directoryList = $directoryList;
    }

    private function getConfig(string $key): string
    {
        return (string) $this->scopeConfig->getValue(self::CONFIG_PATH . $key, ScopeInterface::SCOPE_STORES);
    }

    /**
     * Do file upload to destination host
     *
     * @param string $sourcePath
     * @param string $destinationPath
     *
     * @return array
     */
    public function upload($sourcePath, $destinationPath)
    {
        if (!$this->getConfig('host') || !$this->getConfig('user') || !$this->getConfig('password')) {
            return ['success' => false, 'message' => __('Missing FTP data!')];
        }

        $result = [];
        try {
            if (!$content = file_get_contents($this->directoryList->getPath('var') . '/' . $sourcePath)) {
                $result['success'] = false;
                $result['message'] = __('No export file found!');
            }

            $this->ftp->open([
                'host'     => $this->getConfig('host'),
                'user'     => $this->getConfig('user'),
                'password' => $this->getConfig('password'),
                'ssl'      => true,
                'passive'  => true,
                'port'     => 21,
            ]);

            $this->ftp->write($destinationPath, $content);
            $this->ftp->close();

            $result['success'] = true;
            $result['message'] = '';
        } catch (\Exception $e) {
            $result['success'] = false;
            $result['message'] = __("Can't connect to FTP!");
        }

        return $result;
    }
}
