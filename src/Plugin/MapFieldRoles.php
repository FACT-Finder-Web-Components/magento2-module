<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Plugin;

use Omikron\FactFinder\Communication\Version;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\FieldRoles;

class MapFieldRoles
{
    /** @var CommunicationConfig */
    private $communicationConfig;

    public function __construct(CommunicationConfig $communicationConfig)
    {
        $this->communicationConfig = $communicationConfig;
    }

    /**
     * @param FieldRoles $subject
     * @param callable   $proceed
     * @param array      $fieldRoles
     * @param int        $storeId
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSaveFieldRoles(FieldRoles $subject, callable $proceed, array $fieldRoles, int $storeId)
    {
        $isNg = $this->communicationConfig->getVersion() === Version::NG;
        return $proceed($isNg ? $this->map($fieldRoles) : $fieldRoles, $storeId);
    }

    protected function map(array $fieldRoles): array
    {
        $getRole = $this->getOrEmptyString($fieldRoles);

        return [
            'brand'                 => $getRole('brand'),
            'campaignProductNumber' => $getRole('productNumber'),
            'deeplink'              => $getRole('deeplink'),
            'description'           => $getRole('description'),
            'displayProductNumber'  => $getRole('productNumber'),
            'ean'                   => $getRole('ean'),
            'imageUrl'              => $getRole('imageUrl'),
            'masterArticleNumber'   => $getRole('masterId'),
            'price'                 => $getRole('price'),
            'productName'           => $getRole('productName'),
            'trackingProductNumber' => $getRole('productNumber'),
        ];
    }

    private function getOrEmptyString(array $fieldRoles): callable
    {
        return function (string $key) use ($fieldRoles) {
            return $fieldRoles[$key] ?? '';
        };
    }
}
