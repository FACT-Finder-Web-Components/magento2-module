<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config\Communication;

use Magento\Directory\Model\Currency;
use Magento\Framework\Locale\ResolverInterface;
use Omikron\Factfinder\Api\Config\ParametersSourceInterface;

class CurrencyConfig implements ParametersSourceInterface
{
    /** @var ResolverInterface */
    private $localeResolver;

    /** @var Currency */
    private $currency;

    public function __construct(
        ResolverInterface $localeResolver,
        Currency $currency
    ) {
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
        $currencies = $this->currency->getConfigDefaultCurrencies();
        return reset($currencies );
    }

    private function getCurrencyCountryCode(): string
    {
        return str_replace('_', '-', $this->localeResolver->getLocale());
    }
}
