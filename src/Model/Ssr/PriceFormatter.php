<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Ssr;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\FieldRoles;
use Omikron\FactFinder\Communication\Version;

class PriceFormatter
{
    /** @var PriceCurrencyInterface */
    private $priceCurrency;

    /** @var CommunicationConfig */
    private $communicationConfig;

    /** @var FieldRoles */
    private $fieldRoles;

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

        return array_map($this->price($priceField, $recordField), $records);
    }

    protected function price(string $priceField, string $recordField): callable
    {
        return function (array $record) use ($priceField, $recordField): array {
            $record['record'] = array_merge($record[$recordField], [
                '__ORIG_PRICE__' => $record[$recordField][$priceField],
                $priceField      => $this->priceCurrency->format($record[$recordField][$priceField], false)
            ]);
            return $record;
        };
    }
}
