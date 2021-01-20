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
     * @param \Closure         $proceed
     * @param RequestInterface $request
     * @param ActionInterface  $action
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundValidate(
        $subject,
        \Closure $proceed,
        $request,
        $action
    ) {
        if ($request->getModuleName() === 'factfinder') {
            return;
        }
        $proceed($request, $action);
    }
}
