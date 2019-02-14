<?php

namespace Omikron\Factfinder\Controller;

use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Omikron\Factfinder\Helper\Data;

/**
 * Class Router
 * Custom Router to realize the required factfinder url pattern
 *
 * @package Omikron\Factfinder\Controller
 */
class Router implements RouterInterface
{
    /** @var ActionFactory */
    protected $actionFactory;

    /**
     * Router constructor.
     *
     * @param ActionFactory $actionFactory
     */
    public function __construct(ActionFactory $actionFactory)
    {
        $this->actionFactory = $actionFactory;
    }

    /**
     * Test the incoming requests for matches to the factfinder url pattern
     *
     * @param RequestInterface $request
     * @return bool|ActionInterface
     */
    public function match(RequestInterface $request)
    {
        if (!$this->isValidRequest($request)) {
            return false;
        }

        // check if URL matches = FACT-Finder/result
        $identifier = trim($request->getPathInfo(), '/');
        $pos = strpos($identifier, '/');
        $path = substr($identifier, $pos + 1);

        if ($path == Data::CUSTOM_RESULT_PAGE) {
            $request->setModuleName('factfinder')->setControllerName('result')->setActionName('index');
        } else if ($path == Data::EXPORT_PAGE) {
            $request->setModuleName('factfinder')->setControllerName('export')->setActionName('export');
        } else {
            $request->setModuleName('factfinder')->setControllerName('proxy')->setActionName('call');
        }

        return $this->actionFactory->create(Forward::class);
    }

    /**
     * Check request state to trigger router match
     *
     * @param RequestInterface $request
     * @return bool
     */
    protected function isValidRequest(RequestInterface $request) {
        $pathRegex = '/^(\/' . Data::FRONT_NAME . '\/)/';
        // don't match if controller name is already set
        // also check if URL matches FACT-Finder front name defined in Data helper
        return is_null($request->getControllerName()) && preg_match($pathRegex, $request->getPathInfo());
    }
}
