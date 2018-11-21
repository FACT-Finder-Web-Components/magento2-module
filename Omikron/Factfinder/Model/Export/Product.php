<?php

namespace Omikron\Factfinder\Model\Export;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Class Product
 * @package Omikron\Factfinder\Model\Export
 */
class Product extends AbstractModel
{
    const FEED_PATH = 'factfinder/';
    const FEED_FILE = 'export.';
    const FEED_FILE_FILETYPE = 'csv';
    const BATCH_SIZE = 3000;

    /** @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory */
    protected $productCollectionFactory;

    /** @var \Magento\Catalog\Model\Product\Visibility */
    protected $catalogProductVisibility;

    /** @var \Magento\Catalog\Model\ResourceModel\Product */
    protected $productResource;

    /** @var \Magento\Framework\Pricing\PriceCurrencyInterface */
    protected $priceCurrency;

    /** @var \Magento\Framework\Filesystem */
    protected $fileSystem;

    /** @var  \Omikron\Factfinder\Helper\Upload */
    protected $helperUpload;

    /** @var  \Omikron\Factfinder\Helper\Data */
    protected $helperData;

    /** @var  \Omikron\Factfinder\Helper\Communication */
    protected $helperCommunication;

    /** @var \Omikron\Factfinder\Helper\Product */
    protected $helperProduct;

    /** @var  \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /** @var \Magento\Framework\File\Csv */
    protected $csvWriter;

    /** @var \Magento\Framework\App\Filesystem\DirectoryList */
    protected $directoryList;

    /** @var  \Magento\Store\Model\App\Emulation */
    protected $appEmulation;

    /**
     * Product constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $resourceCollection
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param \Omikron\Factfinder\Helper\Upload $helperUpload
     * @param \Omikron\Factfinder\Helper\Data $helperData
     * @param \Omikron\Factfinder\Helper\Communication $helperCommunication
     * @param \Omikron\Factfinder\Helper\Product $helperProduct
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\File\Csv $csvWriter
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Store\Model\App\Emulation $appEmulation
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $resourceCollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Filesystem $fileSystem,
        \Omikron\Factfinder\Helper\Upload $helperUpload,
        \Omikron\Factfinder\Helper\Data $helperData,
        \Omikron\Factfinder\Helper\Communication $helperCommunication,
        \Omikron\Factfinder\Helper\Product $helperProduct,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\File\Csv $csvWriter,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Store\Model\App\Emulation $appEmulation,
        array $data = []
    )
    {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->productResource = $productResource;
        $this->priceCurrency = $priceCurrency;
        $this->fileSystem = $fileSystem;
        $this->helperUpload = $helperUpload;
        $this->helperData = $helperData;
        $this->helperCommunication = $helperCommunication;
        $this->helperProduct = $helperProduct;
        $this->storeManager = $storeManager;
        $this->csvWriter = $csvWriter;
        $this->directoryList = $directoryList;
        $this->appEmulation = $appEmulation;

        parent::__construct(
            $context,
            $registry,
            $productResource,
            $resourceCollection,
            $data
        );
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
            'Attributes',
            'CategoryPath',
            'Availability',
            'EAN',
            'MagentoEntityId'
        ];
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
            $currChannel = $this->helperData->getChannel($store->getId());
            if (in_array($currChannel, $exportedChannels) || !$this->helperData->isEnabled($store->getId())) {
                continue;
            }

            $exportedChannels[] = $currChannel;

            $result = $this->exportProduct($store);
            if (isset($result['has_errors']) && $result['has_errors']) {
                break;
            }

            if ($updateFieldRoles) {
                $this->helperCommunication->updateFieldRoles($store);
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
        $filename = self::FEED_FILE . $this->helperData->getChannel($store->getId()) . '.' . self::FEED_FILE_FILETYPE;

        $output = $this->buildFeed($store);
        $result = $this->writeFeedToFile($filename, $output);
        if (isset($result['has_errors']) && $result['has_errors']) {
            return $result;
        }
        $result = $this->uploadFeed($filename);
        $this->deleteFeedFile($filename);
        if (isset($result['has_errors']) && $result['has_errors']) {
            return $result;
        }

        if ($this->helperData->isPushImportEnabled($store->getId())) {

            if ($this->helperCommunication->pushImport($this->helperData->getChannel($store->getId()))) {
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
        $filename = self::FEED_FILE . $this->helperData->getChannel($store->getId()) . '.' . self::FEED_FILE_FILETYPE;
        $output = $this->buildFeed($store);

        return array(
            'filename' => $filename,
            'data' => $output
        );
    }

    /**
     * @param StoreInterface $store
     * @return Collection
     */
    private function getFilteredProductCollection($store)
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

            if(!is_dir($fileDirectoryPath)) {
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
     * @param $filename
     * @return bool
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
