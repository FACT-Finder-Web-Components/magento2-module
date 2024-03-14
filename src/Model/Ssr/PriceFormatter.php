<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Ssr;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Omikron\FactFinder\Communication\Version;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\FieldRoles;

class PriceFormatter
{
    public function __construct(
        private readonly CommunicationConfig $communicationConfig,
        private readonly PriceCurrencyInterface $priceCurrency,
        private readonly FieldRoles $fieldRoles,
    ) {
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
