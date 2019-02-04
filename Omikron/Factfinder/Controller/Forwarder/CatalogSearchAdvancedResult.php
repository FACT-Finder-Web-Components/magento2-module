<?php

namespace Omikron\Factfinder\Controller\Forwarder;

use Omikron\Factfinder\Helper\Data;

/**
 * Class CatalogSearchAdvancedResult
 * Get called on Requests to the magento advanced Catalog Search Result Page
 * @package Omikron\Factfinder\Controller\Forwarder
 */
class CatalogSearchAdvancedResult extends \Magento\CatalogSearch\Controller\Advanced\Result
{
    /** @var Data */
    protected $_helper;

    /**
     * CatalogSearchAdvancedResult constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\CatalogSearch\Model\Advanced $catalogSearchAdvanced
     * @param \Magento\Framework\UrlFactory $urlFactory
     * @param \Omikron\Factfinder\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\CatalogSearch\Model\Advanced $catalogSearchAdvanced,
        \Magento\Framework\UrlFactory $urlFactory,
        \Omikron\Factfinder\Helper\Data $helper
    )
    {
        $this->_helper = $helper;
        parent::__construct($context, $catalogSearchAdvanced, $urlFactory);
    }

    /**
     * Forward to ff search if ff is enabled
     */
    public function execute()
    {
        // is FACT-Finder integration enabled
        if ($this->_helper->isEnabled()) {
            // This is used to forward the normal search to FACT-Finder
            $query = $this->_request->getParam('name', $this->_helper->getDefaultQuery());
            $url = $this->_url->getBaseUrl() . Data::FRONT_NAME . '/' . Data::CUSTOM_RESULT_PAGE . '?query=' . $query;
            $this->_redirect($url);
            $this->_response->sendResponse();
        } else {
            // render the normal search
            parent::execute();
        }
    }
}
