<?php

namespace Omikron\Factfinder\Model\Export;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Product
 * @package Omikron\Factfinder\Model\Export
 */
class Product extends AbstractModel
{
    const FEED_PATH = 'factfinder/';
    const FEED_FILE = 'export.';
    const FEED_FILE_FILETYPE = 'csv';
    const PRODUCT_LIMIT = 50000;

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
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    private function getProducts($store)
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addFieldToFilter('visibility',['neq' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE]);
        $collection->setStore($store);
        
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
    private function buildFeedRow($product, $store)
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
     * @param string $delimiter
     * @param string $enclosure
     * @param bool $encloseAll
     *
     * @return string
     */
    private function writeLine(array &$fields, $delimiter = ';', $enclosure = '"', $encloseAll = true)
    {
        $delimiter_esc = preg_quote($delimiter, '/');
        $enclosure_esc = preg_quote($enclosure, '/');

        $output = [];
        foreach ($fields as $field) {
            // Enclose fields containing $delimiter, $enclosure or whitespace
            if ($encloseAll || preg_match("/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field)) {
                $output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
            } else {
                $output[] = $field;
            }
        }

        $lineString = implode($delimiter, $output) . "\n";

        return $lineString;
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
     * Build the Product feed for a specific store
     *
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return string
     */
    private function buildFeed($store)
    {
        $output = '';
        $addHeaderCols = true;
        $productCount = 0;

        /** @var \Magento\Catalog\Model\Product $product */
        $products = $this->getProducts($store);

        foreach ($products as $product) {
            if ($productCount < self::PRODUCT_LIMIT) {
                $rowData = $this->buildFeedRow($product, $store);

                if ($addHeaderCols) {
                    $addHeaderCols = false;

                    $headerCols = array_keys($rowData);
                    $output .= $this->writeLine($headerCols, ';', '"', true);
                }

                $output .= $this->writeLine($rowData, ';', '"', true);
                $productCount++;
            } else {
                break;
            }
        }

        return mb_convert_encoding($output, 'UTF-8');
    }

    /**
     * Write the feed output into a file
     *
     * @param string $filename
     * @param string $output
     * @return array
     */
    private function writeFeedToFile($filename, &$output)
    {

        $result = [];
        $filePath = self::FEED_PATH . $filename;

        $writer = $this->fileSystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $writer->create(self::FEED_PATH);

        $file = $writer->openFile($filePath, 'wb');
        if ($writer->isWritable($filePath)) {
            try {
                $file->write(pack("CCC", 0xef, 0xbb, 0xbf));
                $file->write($output);
            } catch (\Exception $e) {
                $result['has_errors'] = true;
                $result['message'] = __('Error: Could not write file. Error thrown in:' . __METHOD__);
            }
        } else {
            // Show Error Message
            $result['has_errors'] = true;
            $result['message'] = __('Target folder for export file is not writable!');
        }

        return $result;
    }

    /**
     * Delete the specified feed file
     *
     * @param $filename
     * @return bool
     */
    private function deleteFeedFile($filename)
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
    private function uploadFeed($filename)
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
