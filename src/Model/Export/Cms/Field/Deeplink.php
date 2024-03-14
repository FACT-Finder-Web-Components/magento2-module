<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Cms\Field;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\Export\FieldInterface;

class Deeplink implements FieldInterface
{
    public function __construct(
        private readonly UrlInterface $urlBuilder,
        private readonly StoreManagerInterface $storeManager,
    ) {
    }

    public function getName(): string
    {
        return 'Deeplink';
    }

    /**
     * @param PageInterface $page
     * @return string
     */
    public function getValue(AbstractModel $page): string
    {
        $this->urlBuilder->setScope($this->storeManager->getStore()->getId());
        return $this->urlBuilder->getUrl(null, ['_direct' => $page->getIdentifier()]);
    }
}
