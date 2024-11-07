<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config\Backend;

use Exception;
use Magento\Config\Model\Config\Backend\File;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaStorage\Model\File\Uploader;

/**
 * @SuppressWarnings(PHPMD)
 */
class Rsa extends File
{
    public function beforeSave()
    {
        $value = $this->getValue();
        $file = $this->getFileData();

        if (!empty($file)) {
            $uploadDir = $this->_getUploadDir();
            try {
                /** @var Uploader $uploader */
                $uploader = $this->_uploaderFactory->create(['fileId' => $file]);
                $uploader->setAllowedExtensions($this->_getAllowedExtensions());
                $uploader->setAllowRenameFiles(true);
                $uploader->addValidateCallback('size', $this, 'validateMaxSize');
                $result = $uploader->save($uploadDir);
            } catch (Exception $e) {
                throw new LocalizedException(__('%1', $e->getMessage()));
            }
            if ($result !== false) {
                $filename = $result['file'];
                if ($this->_addWhetherScopeInfo()) {
                    $filename = $this->_prependScopeInfo($filename);
                }
                $this->setValue($filename);
            }
        } else {
            if (is_array($value) && !empty($value['delete'])) {
                $this->setValue('');
            } elseif (is_array($value) && !empty($value['value'])) {
                $this->setValueAfterValidation($value['value']);
            } else {
                $this->unsValue();
            }
        }

        return $this;
    }

    protected function getUploadDirPath($uploadDir)
    {
        return $this->_filesystem->getDirectoryWrite(DirectoryList::CONFIG)->getAbsolutePath($uploadDir);
    }

    private function setValueAfterValidation(string $value): void
    {
        // avoid intercepting value
        if (preg_match('/[^a-z0-9_\/\\-\\.]+/i', $value)) {
            throw new LocalizedException(__('Invalid file name'));
        }

        $this->setValue($value);
    }
}
