<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller;

use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;

/**
 * Implementation of CsrfAwareActionInterface.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
trait SkipCsrfValidation
{
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
