<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Cms\Field;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\Export\FieldInterface;
use Magento\Framework\Model\AbstractModel;

class Deeplink implements FieldInterface
{
    private UrlInterface $urlBuilder;
    private StoreManagerInterface $storeManager;

    public function __construct(UrlInterface $urlBuilder, StoreManagerInterface $storeManager)
    {
        $this->urlBuilder   = $urlBuilder;
        $this->storeManager = $storeManager;
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
