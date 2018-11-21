<?php

namespace Omikron\Factfinder\Helper;

use Magento\Catalog\Model\Category;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Framework\Exception\NoSuchEntityException;


/**
 * Class Product
 * Helper to get product and related data from magento
 * @package Omikron\Factfinder\Helper
 */
class Product extends AbstractHelper
{
    const ATTRIBUTE_LIMIT     = 1000;
    const ATTRIBUTE_DELIMITER = '|';

    const PATH_DATA_TRANSFER_MANUFACTURER          = "factfinder/data_transfer/ff_manufacturer";
    const PATH_DATA_TRANSFER_EAN                   = "factfinder/data_transfer/ff_ean";
    const PATH_DATA_TRANSFER_ADDITIONAL_ATTRIBUTES = "factfinder/data_transfer/ff_additional_attributes";
    const PATH_DATA_TRANSFER_PRODUCT_VISIBILITY    = "factfinder/data_transfer/ff_product_visibility";

    /** @var \Magento\Catalog\Helper\Image */
    protected $imageHelperFactory;

    /** @var \Magento\Eav\Model\Config */
    protected $eavConfig;

    /** @var \Magento\Catalog\Model\ProductRepository */
    protected $productRepository;

    /** @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable */
    protected $catalogProductTypeConfigurable;

    /** @var \Magento\Catalog\Api\CategoryRepositoryInterface */
    protected $categoryRepository;

    /**
     * Product constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Helper\Image $imageHelperFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
    )
    {
        $this->imageHelperFactory = $imageHelperFactory;
        $this->eavConfig = $eavConfig;
        $this->productRepository = $productRepository;
        $this->catalogProductTypeConfigurable = $catalogProductTypeConfigurable;
        $this->categoryRepository = $categoryRepository;

        parent::__construct($context);

    }

    /**
     * Get the attribute value from magento product in corresponding store
     *
     * @param $attribute
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return mixed|null
     */
    public function get($attribute, $product, $store)
    {
        //max field number is 128. No Check implemented because number is fixed in this case.
        switch ((string)$attribute) {
            case "ProductNumber":
            case "MasterProductNumber":
            case "Name":
            case "Description":
            case "Short":
            case "ProductUrl":
            case "Price":
            case "CategoryPath":
            case "Availability":
            case "MagentoEntityId":
                $method = 'get' . $attribute;
                return call_user_func(array($this, $method), $product);

            case "ImageUrl":
            case "Manufacturer":
            case "Attributes":
            case "EAN":
                $method = 'get' . $attribute;
                return call_user_func(array($this, $method), $product, $store);

            default:
                return null;
        }
    }

    /**
     * @param StoreInterface $store
     *
     * @return reutrn array
     */
    public function getProductVisibility($store)
    {
        return explode(',', $this->scopeConfig->getValue(self::PATH_DATA_TRANSFER_PRODUCT_VISIBILITY, 'store', $store->getId()));
    }

    /**
     * Get the product number
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
    protected function getProductNumber($product)
    {
        return $product->getData('sku');
    }

    /**
     * Get the product number of the parent product if existing
     *
     * @param integer $id
     * @return false|integer
     */
    protected function getProductParentIdByProductId($id)
    {
        $parentByChild = $this->catalogProductTypeConfigurable->getParentIdsByChild($id);
        $parentId = false;
        if (isset($parentByChild[0])) {
            $parentId = $parentByChild[0];
        }
        return $parentId;
    }

    /**
     * Get the master product number
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
    protected function getMasterProductNumber($product)
    {
        $masterProductNumber = null;
        if ($product->getTypeId() == Configurable::TYPE_CODE) {
            /**
             * It's unnecessary to check parent product of configurable product
             */
            $masterProductNumber = $product->getSku();
        } else {
            $parentId = $this->getProductParentIdByProductId($product->getId());
            try {
                $parentProduct = $this->productRepository->getById($parentId);
                $masterProductNumber = $parentProduct->getSku();
            } catch (NoSuchEntityException $e) {
                $masterProductNumber = $product->getSku();
            }
        }

        return $masterProductNumber;
    }

    /**
     * Get the product name
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
    protected function getName($product)
    {
        return $product->getData('name');
    }

    /**
     * Get the product description
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    protected function getDescription($product)
    {
        return $this->cleanValue($product->getData('description'));
    }

    /**
     * Get the product short description
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    protected function getShort($product)
    {
        return $this->cleanValue($product->getData('short_description'));
    }

    /**
     * Get the product detail page url
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
    protected function getProductUrl($product)
    {
        return $product->getUrlInStore();
    }

    /**
     * Retrieve product thumbnail url
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product $product
     * @return string
     */
    protected function getImageUrl($product, $store)
    {
        $imageId = 'product_thumbnail_image';
        /**@var \Magento\Catalog\Helper\Image $image */
        $image = $this->imageHelperFactory->create()->init($product, $imageId, ['type' => 'thumbnail'])
            ->constrainOnly(true)
            ->keepAspectRatio(true)
            ->keepTransparency(true)
            //->keepFrame(false)
            ->resize(200, 200);

        return $image->getUrl();
    }

    /**
     * Get the product price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    protected function getPrice($product)
    {
        return number_format(round(floatval($product->getData('price')), 2), 2);
    }

    /**
     * Get the product category
     *
     * @param $product
     * @return string
     */
    protected function getCategoryPath($product)
    {
        $categoryIds = $product->getCategoryIds();
        $path = [];
        $attrCount = 0;
        foreach ($categoryIds as $categoryId) {
            /** @var \Magento\Catalog\Api\Data\CategoryInterface $category */
            $category = $this->categoryRepository->get($categoryId);
            if ($attrCount < self::ATTRIBUTE_LIMIT) {
                $categoryPath = $this->getCategoryPathByCategory($category);
                if (!empty($categoryPath)) {
                    $path[] = $categoryPath;
                    $attrCount++;
                }
            }
        }
        return implode(self::ATTRIBUTE_DELIMITER, $path);
    }

    /**
     * Returns category path as url encoded category names separated by slashes
     *
     * @param \Magento\Catalog\Api\Data\CategoryInterface $category
     * @return string
     */
    protected function getCategoryPathByCategory($category) {
        if (in_array($category->getParentId(), [Category::ROOT_CATEGORY_ID, Category::TREE_ROOT_ID])) {
            return '';
        }
        $path = urlencode($category->getName());
        $parentPath = $this->getCategoryPathByCategory($this->categoryRepository->get($category->getParentId()));
        $path = $parentPath === '' ? $path : $parentPath . '/' . $path;
        return $path;
    }

    /**
     * Get if the product is available
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return int
     */
    protected function getAvailability($product)
    {
        return (int)$product->isAvailable();
    }

    /**
     * Get the magento product entity id
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return int
     */
    protected function getMagentoEntityId($product)
    {
        return $product->getId();
    }

    /**
     * Get if the product manufacturer
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return mixed
     */
    protected function getManufacturer($product, $store)
    {
        return $product->getData($this->scopeConfig->getValue(self::PATH_DATA_TRANSFER_MANUFACTURER, 'store', $store->getId()));
    }

    /**
     * Get if the product ean
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return mixed
     */
    protected function getEAN($product, $store)
    {
        return $product->getData($this->scopeConfig->getValue(self::PATH_DATA_TRANSFER_EAN, 'store', $store->getId()));
    }


    /**
     * Get the additional attribute fields for the store
     *
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return mixed
     */
    protected function getAdditionalAttributes($store)
    {
        return $this->scopeConfig->getValue(self::PATH_DATA_TRANSFER_ADDITIONAL_ATTRIBUTES, 'store', $store->getId());
    }

    /**
     * Get all the attributes for a given product and store
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product $product
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return string
     */
    protected function getAttributes($product, $store)
    {
        $data = [];
        $attributesString = '';
        $additionalAttributes = $this->getAdditionalAttributes($store);
        if (!empty($additionalAttributes)) {
            $attributeCodes = explode(',', $additionalAttributes);
            $attrCount = 0;

            foreach ($attributeCodes as $attributeCode) {
                $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
                $attributeValue = $product->getData($attribute->getAttributeCode());
                if (empty($attributeValue)) {
                    continue;
                }
                $frontendInput = $attribute->getFrontendInput();
                $values = [];
                if (in_array($frontendInput, ['select', 'multiselect'])) {
                    // value holds single or multiple options IDs
                    foreach (explode(",", $attributeValue) as $optionId) {
                        $optionLabel = $attribute->getSource()->getOptionText($optionId);
                        $values[] = $optionLabel;
                    }
                } else if ($frontendInput == 'price') {
                    $values[] = number_format(round(floatval($attributeValue), 2), 2);
                } else if ($frontendInput == 'boolean') {
                    $values[] = $attributeValue ? "Yes" : "No";
                } else {
                    $values[] = $attributeValue;
                }

                $label = $product->getResource()->getAttribute($attribute->getAttributeCode())->getStoreLabel();
                if (empty($label)) {
                    $label = $attribute->getAttributeCode();
                }

                foreach ($values as $value) {
                    if ($attrCount < self::ATTRIBUTE_LIMIT) {
                        $data[] = __($this->cleanValue($label, true)) . '=' . __($this->cleanValue($value, true));
                        $attrCount++;
                    }
                }
            }

            if (!empty($data)) {
                $attributesString = self::ATTRIBUTE_DELIMITER . implode(self::ATTRIBUTE_DELIMITER, $data) . self::ATTRIBUTE_DELIMITER;
            }
        }
        return $attributesString;
    }


    /**
     * Cleanup a value for export
     *
     * @param $value
     * @param boolean $isMultiAttributeValue
     *
     * @return string
     */
    protected function cleanValue($value, $isMultiAttributeValue = false)
    {
        $value = strip_tags(nl2br($value));
        $value = preg_replace("/\r|\n/", "", $value);
        $value = addcslashes($value, '\\');

        if ($isMultiAttributeValue) {
            // do not allow special chars in values
            $value = preg_replace('/([^A-Za-z0-9 -])+/', '', $value);
            // reduce multiple spaces to one
            $value = preg_replace('/\s\s+/', ' ', $value);
        }

        return trim($value);
    }

}