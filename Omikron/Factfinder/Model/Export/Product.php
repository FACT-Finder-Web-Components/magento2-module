<?php

namespace Omikron\Factfinder\Model\Export;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Helper\Data;
use Omikron\Factfinder\Helper\Product as ProductHelper;
use Omikron\Factfinder\Helper\Upload;
use Omikron\Factfinder\Model\Api\PushImport;
use Omikron\Factfinder\Model\Api\UpdateFieldRoles;

class Product
{
    const FEED_PATH                         = 'factfinder/';
    const FEED_FILE                         = 'export.';
    const FEED_FILE_FILETYPE                = 'csv';
    const BATCH_SIZE                        = 3000;
    const ADDITIONAL_ATTRIBUTES_COLUMN_NAME = 'Attributes';

    /** @var CollectionFactory  */
    protected $productCollectionFactory;

    /** @var Filesystem  */
    protected $fileSystem;

    /** @var Upload  */
    protected $helperUpload;

    /** @var Data  */
    protected $helperData;

    /** @var PushImport  */
    protected $pushImport;

    /** @var UpdateFieldRoles */
    protected $updateFieldRoles;

    /** @var ProductHelper */
    protected $helperProduct;

    /** @var CommunicationConfigInterface  */
    protected $communicationConfig;

    /** @var StoreManagerInterface  */
    protected $storeManager;

    /** @var Csv  */
    protected $csvWriter;

    /** @var DirectoryList  */
    protected $directoryList;

    /** @var Emulation  */
    protected $appEmulation;

    public function __construct(
        CollectionFactory $productCollectionFactory,
        Filesystem $fileSystem,
        Upload $helperUpload,
        Data $helperData,
        PushImport $pushImport,
        UpdateFieldRoles $updateFieldRoles,
        ProductHelper $helperProduct,
        CommunicationConfigInterface $communicationConfig,
        StoreManagerInterface $storeManager,
        Csv $csvWriter,
        DirectoryList $directoryList,
        Emulation $appEmulation
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->fileSystem               = $fileSystem;
        $this->helperUpload             = $helperUpload;
        $this->helperData               = $helperData;
        $this->pushImport               = $pushImport;
        $this->updateFieldRoles         = $updateFieldRoles;
        $this->helperProduct            = $helperProduct;
        $this->storeManager             = $storeManager;
        $this->csvWriter                = $csvWriter;
        $this->directoryList            = $directoryList;
        $this->appEmulation             = $appEmulation;
        $this->communicationConfig      = $communicationConfig;
    }

    /**
     * Get all products for a specific store
     *
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @param int $currentOffset
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function getProductsBatch($store, $currentOffset)
    {
        $collection = $this->getFilteredProductCollection($store);
        $collection->addAttributeToSelect('*');
        $collection->getSelect()->limit(self::BATCH_SIZE, $currentOffset);

        return $collection;
    }

    /**
     * Build a row for the product feed
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Store\Api\Data\StoreInterface $store
     *
     * @return array
     */
    protected function buildFeedRow($product, $store)
    {
        $row = [];
        $attributes = [
            'ProductNumber',
            'MasterProductNumber',
            'Name',
            'Description',
            'Short',
            'ProductUrl',
            'ImageUrl',
            'Price',
            'Manufacturer',
            'CategoryPath',
            'Availability',
            'EAN',
            'MagentoEntityId'
        ];

        if ($this->helperProduct->getAdditionalAttributesExportedInSeparateColumns($store)) {
            $additionalAttributes = $this->helperProduct->getAdditionalAttributes($store);
            if (!empty($additionalAttributes)) {
                $attributes = \array_unique(\array_merge($attributes, explode(',', $additionalAttributes)));
            }
        } else {
            $attributes[]= self::ADDITIONAL_ATTRIBUTES_COLUMN_NAME;
        }

        foreach ($attributes as $attribute) {
            $row[$attribute] = $this->helperProduct->get($attribute, $product, $store);
        }

        return $row;
    }

    /**
     * Write a line into the product feed
     *
     * @param array $fields
     *
     * @return array
     */
    protected function writeLine(array $fields)
    {
        $output = [];
        foreach ($fields as $field) {
            $output[] = $field;
        }

        return $output;
    }

    /**
     * Export all the products for all stores
     *
     * @param bool $updateFieldRoles
     * @return array
     */
    public function exportProducts($updateFieldRoles = false)
    {
        $stores = $this->storeManager->getStores();
        $exportedChannels = [];
        $result = [];

        foreach ($stores as $store) {
            $storeId = $store->getId();
            $currChannel = $this->communicationConfig->getChannel($storeId);
            if (in_array($currChannel, $exportedChannels) || !$this->helperData->isEnabled($storeId)) {
                continue;
            }

            $exportedChannels[] = $currChannel;

            $result = $this->exportProduct($store);
            if (isset($result['has_errors']) && $result['has_errors']) {
                break;
            }

            if ($updateFieldRoles) {
                $this->updateFieldRoles->execute($storeId);
            }
        }
        return $result;
    }

    /**
     * Export all products for a specific store
     *
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return array
     */
    public function exportProduct($store)
    {
        $storeId  = $store->getId();
        $channel  = $this->communicationConfig->getChannel($storeId);
        $filename = self::FEED_FILE . $channel . '.' . self::FEED_FILE_FILETYPE;

        $output = $this->buildFeed($store);
        $result = $this->writeFeedToFile($filename, $output);

        if ($result['has_errors'] ?? []) {
            return $result;
        }

        $result = $this->uploadFeed($filename);
        $this->deleteFeedFile($filename);

        if ($this->helperData->isPushImportEnabled($storeId)) {

            if ($this->pushImport->execute([], $storeId)) {
                $result['message'] .= ' ' . __('Import successfully pushed.');
            } else {
                $result['message'] .= ' ' . __('Import not successful.');
            }
        }
        return $result;
    }

    /**
     * Export all products for a specific store
     * using external url
     *
     * @param \Magento\Store\Api\Data\StoreInterface $store
     *
     * @return array
     */
    public function exportProductWithExternalUrl($store)
    {
        $filename = self::FEED_FILE . $this->communicationConfig->getChannel($store->getId()) . '.' . self::FEED_FILE_FILETYPE;
        $output = $this->buildFeed($store);

        return [
            'filename' => $filename,
            'data' => $output
        ];
    }

    /**
     * @param StoreInterface $store
     * @return Collection
     */
    protected function getFilteredProductCollection($store)
    {
        /** @var Collection $collection */
        return $this->productCollectionFactory
            ->create()
            ->clear()
            ->addWebsiteFilter($store->getWebsiteId())
            ->setStore($store)
            ->addFieldToFilter('visibility', ['in' => $this->helperProduct->getProductVisibility($store)])
            ->addAttributeToFilter('status', Status::STATUS_ENABLED);
    }

    /**
     * Build the Product feed for a specific store
     *
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return array
     */
    protected function buildFeed($store)
    {
        $this->appEmulation->startEnvironmentEmulation($store->getId(), \Magento\Framework\App\Area::AREA_FRONTEND, true);

        $output        = [];
        $addHeaderCols = true;
        $productCount  = $this->getFilteredProductCollection($store)->getSize();
        $currentOffset = 0;

        while ($currentOffset < $productCount) {
            $products = $this->getProductsBatch($store, $currentOffset);

            /** @var \Magento\Catalog\Model\Product $product */
            foreach ($products as $product) {
                $rowData = $this->buildFeedRow($product, $store);

                if ($addHeaderCols) {
                    $addHeaderCols = false;
                    $output[]      = array_keys($rowData);
                }

                $output[] = $this->writeLine($rowData);
            }

            $currentOffset += $products->count();
        }

        $this->appEmulation->stopEnvironmentEmulation();

        return $output;
    }

    /**
     * Write the feed output into a file
     *
     * @param string $filename
     * @param string $output
     * @return array
     */
    protected function writeFeedToFile($filename, &$output)
    {
        $result = [];

        try {
            $fileDirectoryPath = $this->directoryList->getPath(DirectoryList::VAR_DIR);

            if (!is_dir($fileDirectoryPath)) {
                mkdir($fileDirectoryPath, 0777, true);
            }

            $filePath =  $fileDirectoryPath . '/' . self::FEED_PATH . $filename;

            $this->csvWriter
                ->setEnclosure('"')
                ->setDelimiter(';')
                ->saveData($filePath, $output);
        } catch (\Exception $e) {
            $result['has_errors'] = true;
            $result['message'] = 'Error: Could not write file' . ' - ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Delete the specified feed file
     *
     * @param string $filename
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function deleteFeedFile($filename)
    {
        $filePath = self::FEED_PATH . $filename;
        $writer = $this->fileSystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        if ($writer->isExist($filePath)) {
            $writer->delete($filePath);
        }
        return true;
    }

    /**
     * Upload the specified product feed file to factfinder
     *
     * @param string $filename
     * @return array
     */
    protected function uploadFeed($filename)
    {
        $result = [];

        $uploadResult = $this->helperUpload->upload(self::FEED_PATH . $filename, $filename);

        if ($uploadResult['success']) {
            $result['has_errors'] = false;
            $result['message'] = __('File uploaded successfully!');
        } else {
            $result['has_errors'] = true;
            $result['message'] = $uploadResult['message'];
        }

        return $result;
    }
}
