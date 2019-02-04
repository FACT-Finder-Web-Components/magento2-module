<?php

namespace Omikron\Factfinder\Controller\Proxy;

use Magento\Framework\Webapi\Exception;

/**
 * Class Call
 * Forward the ff-api calls to factfinder
 *
 * @package Omikron\Factfinder\Controller\Proxy
 */
class Call extends \Magento\Framework\App\Action\Action
{
    /** @var \Magento\Framework\Controller\Result\JsonFactory */
    protected $_jsonResultFactory;

    /** @var \Omikron\Factfinder\Helper\ResultRefiner */
    protected $_resultRefiner;

    /** @var \Omikron\Factfinder\Helper\Communication */
    protected $_communication;

    /**
     * Call constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
     * @param \Omikron\Factfinder\Helper\ResultRefiner $resultRefiner
     * @param \Omikron\Factfinder\Helper\Communication $communication
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Omikron\Factfinder\Helper\ResultRefiner $resultRefiner,
        \Omikron\Factfinder\Helper\Communication $communication
    )
    {
        parent::__construct($context);
        $this->_jsonResultFactory = $jsonResultFactory;
        $this->_resultRefiner = $resultRefiner;
        $this->_communication = $communication;
    }

    /**
     * Forward the ff-api calls to factfinder and return the response of factfinder as response
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        // extract api name from path
        $identifier = trim($this->getRequest()->getPathInfo(), '/');
        $pos = strpos($identifier, '/');
        $path = substr($identifier, $pos+1);
        $apiNameRegex = '/^[A-Z][A-z]+(.ff)$/';
        $matches = [];
        preg_match($apiNameRegex, $path, $matches);

        $result = $this->_jsonResultFactory->create();

        // return 404 if api name schema does not match
        if (empty($matches)) {
            $result->setHttpResponseCode(Exception::HTTP_NOT_FOUND);
            return $result;
        }

        // get api name from regex matches
        $apiName = $matches[0];

        $ffResponse = $this->_communication->sendToFF($apiName, $this->getRequest()->getParams());

        return $result->setJsonData($this->_resultRefiner->refine($ffResponse));
    }
}
