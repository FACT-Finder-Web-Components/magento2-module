<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Plugin;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\CsrfValidator;
use Magento\Framework\App\RequestInterface;

class CsrfValidatorSkip
{
    /**
     * @param CsrfValidator    $subject
     * @param callable         $proceed
     * @param RequestInterface $request
     * @param ActionInterface  $action
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundValidate($subject, callable $proceed, $request, $action)
    {
        if ($request->getModuleName() === 'factfinder') {
            return;
        }
        $proceed($request, $action);
    }
}
