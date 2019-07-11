<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config\Communication;

use Magento\Directory\Model\Currency;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\Config\ParametersSourceInterface;

class CurrencyConfig implements ParametersSourceInterface
{
    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var ResolverInterface */
    private $localeResolver;

    public function __construct(ScopeConfigInterface $scopeConfig, ResolverInterface $localeResolver)
    {
        $this->scopeConfig    = $scopeConfig;
        $this->localeResolver = $localeResolver;
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
        return (string) $this->scopeConfig->getValue(Currency::XML_PATH_CURRENCY_DEFAULT, ScopeInterface::SCOPE_STORES);
    }

    private function getCurrencyCountryCode(): string
    {
        return str_replace('_', '-', $this->localeResolver->getLocale());
    }
}
