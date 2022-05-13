<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Cms;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Model\Config\CmsConfig;
use Traversable;

class Pages implements \IteratorAggregate
{
    private PageRepositoryInterface $pageRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private CmsConfig $cmsConfig;
    private StoreManagerInterface $storeManager;

    public function __construct(
        PageRepositoryInterface $pageRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CmsConfig $cmsConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->pageRepository        = $pageRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->cmsConfig             = $cmsConfig;
        $this->storeManager          = $storeManager;
    }

    /**
     * @return Traversable|PageInterface[]
     * @throws LocalizedException
     */
    public function getIterator(): Traversable
    {
        $query = $this->getQuery()->create();
        yield from $this->pageRepository->getList($query)->getItems();
    }

    protected function getQuery(): SearchCriteriaBuilder
    {
        $blacklist = $this->cmsConfig->getCmsBlacklist();
        if ($blacklist) {
            $this->searchCriteriaBuilder->addFilter('identifier', $blacklist, 'nin');
        }

        $inStores  = [Store::DEFAULT_STORE_ID, (int) $this->storeManager->getStore()->getId()];
        return $this->searchCriteriaBuilder->addFilter('store_id', $inStores, 'in');
    }
}
