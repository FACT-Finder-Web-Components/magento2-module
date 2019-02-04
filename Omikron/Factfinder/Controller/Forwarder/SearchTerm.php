<?php

namespace Omikron\Factfinder\Controller\Forwarder;

use Omikron\Factfinder\Helper\Data;

/**
 * Class SearchTerm
 * Get called on requests to the magento catalog search
 * @package Omikron\Factfinder\Controller\Forwarder
 */
class SearchTerm extends \Magento\Search\Controller\Term\Popular
{
    /** @var Data */
    protected $_helper;

    /**
     * SearchTerm constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Omikron\Factfinder\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Omikron\Factfinder\Helper\Data $helper
    )
    {
        $this->_helper = $helper;
        parent::__construct($context, $scopeConfig);
    }

    /**
     * Forward to ff search if ff is enabled
     */
    public function execute()
    {
        // is FACT-Finder integration enabled
        if ($this->_helper->isEnabled()) {
            // This is used to forward the Page to FACT-Finder
            $url = $this->_url->getBaseUrl() . Data::FRONT_NAME . '/' . Data::CUSTOM_RESULT_PAGE;
            $this->_redirect($url);
            $this->_response->sendResponse();
        } else {
            // render the normal page
            return parent::execute();
        }
    }
}
