<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Cms;

use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Omikron\Factfinder\Model\Config\CmsConfig;

class Pages implements \IteratorAggregate
{
    /** @var PageRepositoryInterface */
    private $pageRepository;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /** @var CmsConfig  */
    private $cmsConfig;

    public function __construct(
        PageRepositoryInterface $pageRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CmsConfig $cmsConfig
    ) {
        $this->pageRepository        = $pageRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->cmsConfig             = $cmsConfig;
    }

    /**
     * @return \Traversable|PageInterface\[]
     * @throws LocalizedException
     */
    public function getIterator()
    {
        $query = $this->getQuery()->create();
        $list  = $this->pageRepository->getList($query);
        yield from $list->getItems();
    }

    protected function getQuery(): SearchCriteriaBuilder
    {
        return $this->searchCriteriaBuilder->addFilter('identifier', $this->cmsConfig->getCmsBlacklist(), 'nin');
    }
}
