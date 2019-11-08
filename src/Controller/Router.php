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
    public const  FRONT_NAME         = 'FACT-Finder';
    private const EXPORT_PAGE        = 'export';
    private const CUSTOM_RESULT_PAGE = 'result';

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
     *
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

        $request->setModuleName('factfinder')->setActionName('call')->setControllerName('proxy');
        if ($path == self::CUSTOM_RESULT_PAGE) {
            $request->setActionName('index')->setControllerName('result');
        } elseif ($path == self::EXPORT_PAGE) {
            $request->setActionName('export')->setControllerName('export');
        }

        return $this->actionFactory->create(Forward::class);
    }

    private function isValidRequest(RequestInterface $request): bool
    {
        /*
         * Don't match if controller name is already set
         * also check if URL matches FACT-Finder front name
         */
        return !$request->getControllerName() && preg_match('#^(/' . self::FRONT_NAME . '/)#', $request->getPathInfo());
    }
}
