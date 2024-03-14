<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;

class Router implements RouterInterface
{
    public const FRONT_NAME = 'fact-finder';

    /**
     * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
     * phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine
     */
    public function __construct(private readonly ActionFactory $actionFactory)
    {
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

        $request->setModuleName('factfinder');
        $request->setActionName('call');
        $request->setControllerName('proxy');
        return $this->actionFactory->create(Proxy\Call::class);
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
