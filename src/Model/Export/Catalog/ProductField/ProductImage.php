<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductField;

use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\Model\AbstractModel;
use Omikron\Factfinder\Api\Export\FieldInterface;

class ProductImage implements FieldInterface
{
    private ImageHelper $imageHelper;
    private string $imageId;

    public function __construct(ImageHelper $imageHelper, string $imageId = 'ff_export_image_url')
    {
        $this->imageHelper = $imageHelper;
        $this->imageId     = $imageId;
    }

    public function getName(): string
    {
        return 'ImageUrl';
    }

    public function getValue(AbstractModel $product): string
    {
        return $this->imageHelper->init($product, $this->imageId)->getUrl();
    }
}
