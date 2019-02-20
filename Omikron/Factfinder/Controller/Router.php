<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller;

use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;

class Router implements RouterInterface
{
    public const   FRONT_NAME         = 'FACT-Finder';
    private const  EXPORT_PAGE        = 'export';
    private const  CUSTOM_RESULT_PAGE = 'result';

    /** @var ActionFactory */
    private $actionFactory;

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
        $pos        = strpos($identifier, '/');
        $path       = substr($identifier, $pos + 1);

        if ($path == self::CUSTOM_RESULT_PAGE) {
            $request->setModuleName('factfinder')->setControllerName('result')->setActionName('index');
        } else if ($path == self::EXPORT_PAGE) {
            $request->setModuleName('factfinder')->setControllerName('export')->setActionName('export');
        } else {
            $request->setModuleName('factfinder')->setControllerName('proxy')->setActionName('call');
        }

        return $this->actionFactory->create(Forward::class);
    }

    private function isValidRequest(RequestInterface $request): bool
    {
        /*
         * Don't match if controller name is already set
         * also check if URL matches FACT-Finder front name
         */
        return is_null($request->getControllerName()) &&
            preg_match('/^(\/' . self::FRONT_NAME . '\/)/', $request->getPathInfo());
    }
}
