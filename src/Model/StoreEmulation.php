<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Store\Model\App\Emulation;

class StoreEmulation
{
    public function __construct(private readonly Emulation $emulation, private readonly AreaList $areaList)
    {
    }

    public function runInStore(int $storeId, callable $proceed)
    {
        try {
            $this->emulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);

            $area = $this->areaList->getArea(Area::AREA_FRONTEND);
            $area->load(Area::PART_TRANSLATE);

            $proceed();
        } finally {
            $this->emulation->stopEnvironmentEmulation();
        }
    }
}
