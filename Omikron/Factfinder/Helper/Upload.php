<?php

namespace Omikron\Factfinder\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Upload
 * Upload the exported product data to factfinder
 * @package Omikron\Factfinder\Helper
 */
class Upload extends AbstractHelper
{
    const CONFIG_PATH = 'factfinder/data_transfer/';

    /** @var \Magento\Framework\Filesystem\Io\Ftp */
    protected $ftp;
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;
    /** @var \Magento\Framework\App\Filesystem\DirectoryList */
    protected $directoryList;

    /**
     * Upload constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Filesystem\Io\Ftp $ftp
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem\Io\Ftp $ftp,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        $this->ftp = $ftp;
        $this->logger = $context->getLogger();
        $this->directoryList = $directoryList;
        parent::__construct($context);
    }

    /**
     * Get the ff config data
     *
     * @param string $key
     * @return mixed
     */
    protected function getConfig($key)
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH . $key, 'store');
    }

    /**
     * Do file upload to destination host
     *
     * @param string $sourcePath
     * @param string $destinationPath
     * @return array
     */
    public function upload($sourcePath, $destinationPath)
    {
        $result = [];

        if (empty($this->getConfig('ff_upload_host')) || empty($this->getConfig('ff_upload_user')) || empty($this->getConfig('ff_upload_password'))) {
            $result['success'] = false;
            $result['message'] = __('Missing FTP data!');
        } else {
            if (!$content = file_get_contents($this->directoryList->getPath('var') . '/' . $sourcePath)) {
                $result['success'] = false;
                $result['message'] = __('No export file found!');
            }

            try {
                $this->ftp->open(
                    [
                        'host' => $this->getConfig('ff_upload_host'),
                        'user' => $this->getConfig('ff_upload_user'),
                        'password' => $this->getConfig('ff_upload_password'),
                        'ssl' => true,
                        'passive' => true,
                        'port' => 21
                    ]
                );

                $this->ftp->write($destinationPath, $content);
                $this->ftp->close();

                $result['success'] = true;
                $result['message'] = '';
            } catch (\Exception $e) {
                $result['success'] = false;
                $result['message'] = __("Can't connect to FTP!");
            }
        }

        return $result;
    }
}
