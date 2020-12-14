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

class Pages implements \IteratorAggregate
{
    /** @var PageRepositoryInterface */
    private $pageRepository;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /** @var CmsConfig */
    private $cmsConfig;

    /** @var StoreManagerInterface */
    private $storeManager;

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
     * @return \Traversable|PageInterface[]
     * @throws LocalizedException
     */
    public function getIterator()
    {
        $query = $this->getQuery()->create();
        yield from $this->pageRepository->getList($query)->getItems();
    }

    protected function getQuery(): SearchCriteriaBuilder
    {
        $blacklist = $this->cmsConfig->getCmsBlacklist();
        if (!empty($blacklist)) {
          $this->searchCriteriaBuilder->addFilter('identifier', $blacklist, 'nin');
        }
        return $this->searchCriteriaBuilder->addFilter('store_id', [Store::DEFAULT_STORE_ID, (int) $this->storeManager->getStore()->getId()], 'in');
    }
}
