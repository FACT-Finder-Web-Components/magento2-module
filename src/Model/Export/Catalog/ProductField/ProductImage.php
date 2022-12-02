<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductField;

use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\Model\AbstractModel;
use Omikron\Factfinder\Api\Export\FieldInterface;

class ProductImage implements FieldInterface
{
    public function __construct(
        private readonly ImageHelper $imageHelper,
        private readonly string $imageId = 'ff_export_image_url')
    {
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
