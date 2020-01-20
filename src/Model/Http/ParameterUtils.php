<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Http;

use Magento\Framework\App\RequestInterface;

class ParameterUtils
{
    public function fixedGetParams(RequestInterface $request): array
    {
        $params = $request->getParams();
        return array_combine(array_map(function ($key) {
            return boolval(preg_match('/^filter(.*)ROOT/', $key)) ? str_replace('_', '+', $key) : $key;
        }, array_keys($params)), array_values($params));
    }
}
