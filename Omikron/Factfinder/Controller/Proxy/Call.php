<?php

declare(strict_types = 1);

namespace Omikron\Factfinder\Controller\Proxy;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Webapi\Exception;
use Magento\Framework\Controller\Result\JsonFactory ;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Helper\ResultRefiner ;

/**
 * Class Call
 * Forward the ff-api calls to factfinder
 */
class Call extends \Magento\Framework\App\Action\Action
{
    /** @var JsonFactory  */
    protected $jsonResultFactory;

    /** @var ResultRefiner  */
    protected $resultRefiner;

    /** @var ClientInterface  */
    protected $factFinderClient;

    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        ResultRefiner $resultRefiner,
        ClientInterface $factFinderClient
    )
    {
        parent::__construct($context);
        $this->jsonResultFactory = $jsonResultFactory;
        $this->resultRefiner = $resultRefiner;
        $this->factFinderClient = $factFinderClient;
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

        $result = $this->jsonResultFactory->create();

        // return 404 if api name schema does not match
        if (empty($matches)) {
            $result->setHttpResponseCode(Exception::HTTP_NOT_FOUND);
            return $result;
        }

        // get api name from regex matches
        $apiName = $matches[0];

        $ffResponse = $this->factFinderClient->sendToFF($apiName, http_build_query($this->getRequest()->getParams()));

        return $result->setJsonData($this->resultRefiner->refine($ffResponse));
    }
}
