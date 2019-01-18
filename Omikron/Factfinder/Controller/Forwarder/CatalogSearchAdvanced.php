<?php

namespace Omikron\Factfinder\Controller\Forwarder;

use Omikron\Factfinder\Helper\Data;

/**
 * Class CatalogSearchAdvanced
 * Get called on Requests to the magento advanced Catalog Search
 * @package Omikron\Factfinder\Controller\Forwarder
 */
class CatalogSearchAdvanced extends \Magento\CatalogSearch\Controller\Advanced\Index
{
    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $_scopeConfig;

    /** @var \Omikron\Factfinder\Helper\Data $_helper */
    protected $_helper;

    /**
     * CatalogSearchAdvanced constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Omikron\Factfinder\Helper\Data $helper
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Omikron\Factfinder\Helper\Data $helper)
    {
        $this->_helper = $helper;
        parent::__construct($context);
    }

    /**
     * Forward to ff search if ff is enabled
     */
    public function execute()
    {
        // is FACT-Finder integration enabled
        if ($this->_helper->isEnabled()) {
            // this is used to forward the normal search to FACT-Finder
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
