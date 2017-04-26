<?php

namespace Omikron\Factfinder\Controller\Forwarder;

use Omikron\Factfinder\Helper\Data;

/**
 * Class CatalogSearchResult
 * Get called on requests to the magento catalog search result page
 * @package Omikron\Factfinder\Controller\Forwarder
 */
class CatalogSearchResult extends \Magento\CatalogSearch\Controller\Result\Index
{
    /** @var Data */
    protected $_helper;

    /**
     * CatalogSearchResult constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Search\Model\QueryFactory $queryFactory
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Omikron\Factfinder\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Search\Model\QueryFactory $queryFactory,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Omikron\Factfinder\Helper\Data $helper
    )
    {
        $this->_helper = $helper;
        parent::__construct($context, $catalogSession, $storeManager, $queryFactory, $layerResolver);
    }

    /**
     * Forward to ff search if ff is enabled
     */
    public function execute()
    {
        // is FACT-Finder integration enabled
        if ($this->_helper->isEnabled()) {
            // This is used to forward the normal search to FACT-Finder
            $query = $this->_request->getParam('q', $this->_helper->getDefaultQuery());
            $url = $this->_url->getBaseUrl() . Data::FRONT_NAME . '/' . Data::CUSTOM_RESULT_PAGE . '?query=' . $query;
            $this->_redirect($url);
            $this->_response->sendResponse();
        } else {
            // render the normal search
            parent::execute();
        }
    }
}