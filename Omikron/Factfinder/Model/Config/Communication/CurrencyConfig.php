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
    private const  PATH_CURRENCY_MIN_DIGITS = 'factfinder/currency/min_digits';
    private const  PATH_CURRENCY_MAX_DIGITS = 'factfinder/currency/max_digits';
    private const  PATH_CURRENCY_FIELDS     = 'factfinder/currency/fields';

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
            'currency-code'         => [
                'value' => $this->getCurrencyCode(),
                'type'  => 'string',
            ],
            'currency-country-code' => [
                'value' => $this->getCurrencyCountryCode(),
                'type'  => 'string',
            ],
            'currency-fields'       => [
                'value' => $this->getCurrencyFields(),
                'type'  => 'string',
            ],
            'currency-min-digits'   => [
                'value' => $this->getCurrencyMinDigits(),
                'type'  => 'string',
            ],
            'currency-max-digits'   => [
                'value' => $this->getCurrencyMaxDigits(),
                'type'  => 'string',
            ]
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

    private function getCurrencyFields(): string
    {
        return (string) $this->scopeConfig->getValue(self::PATH_CURRENCY_FIELDS, ScopeInterface::SCOPE_STORES);
    }

    private function getCurrencyMinDigits(): int
    {
        return (int) $this->scopeConfig->getValue(self::PATH_CURRENCY_MIN_DIGITS, ScopeInterface::SCOPE_STORES);
    }

    private function getCurrencyMaxDigits(): int
    {
        return (int) $this->scopeConfig->getValue(self::PATH_CURRENCY_MAX_DIGITS, ScopeInterface::SCOPE_STORES);
    }
}
