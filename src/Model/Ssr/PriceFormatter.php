<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Ssr;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\FactFinder\Communication\Version;
use Omikron\Factfinder\Model\FieldRoles;

class PriceFormatter
{
    private PriceCurrencyInterface $priceCurrency;
    private CommunicationConfig $communicationConfig;
    private FieldRoles $fieldRoles;

    public function __construct(
        CommunicationConfig $communicationConfig,
        PriceCurrencyInterface $priceCurrency,
        FieldRoles $fieldRoles
    ) {
        $this->communicationConfig = $communicationConfig;
        $this->priceCurrency       = $priceCurrency;
        $this->fieldRoles          = $fieldRoles;
    }

    public function format(array $searchResult): array
    {
        $priceField  = $this->fieldRoles->getFieldRole('price');
        $isNG        = $this->communicationConfig->getVersion() === Version::NG;
        $records     = $isNG ? $searchResult['hits'] : $searchResult['searchResult']['records'];
        $recordField = $isNG ? 'masterValues' : 'record';

        return ['records' => array_map($this->price($priceField, $recordField), $records)] + $searchResult;
    }

    protected function price(string $priceField, string $recordField): callable
    {
        return function (array $record) use ($priceField, $recordField): array {
            $record['record'] = [
                    '__ORIG_PRICE__' => $record[$recordField][$priceField],
                    $priceField      => $this->priceCurrency->format($record[$recordField][$priceField], false)
                ] + $record[$recordField];

            return $record;
        };
    }
}
