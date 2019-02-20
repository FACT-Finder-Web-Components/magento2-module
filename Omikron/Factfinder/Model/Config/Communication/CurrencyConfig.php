<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config\Communication;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\CurrencyInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\Config\ParametersSourceInterface;

class CurrencyConfig implements ParametersSourceInterface
{
    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var ResolverInterface */
    private $localeResolver;

    /** @var CurrencyInterface */
    private $currency;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ResolverInterface $localeResolver,
        CurrencyInterface $currency
    ) {
        $this->scopeConfig    = $scopeConfig;
        $this->localeResolver = $localeResolver;
        $this->currency       = $currency;
    }

    public function getParameters(): array
    {
        return [
            'currency-code'         => $this->getCurrencyCode(),
            'currency-country-code' => $this->getCurrencyCountryCode(),
        ];
    }

    private function getCurrencyCode(): string
    {
        return $this->currency->getShortName();
    }

    private function getCurrencyCountryCode(): string
    {
        return str_replace('_', '-', $this->localeResolver->getLocale());
    }
}
