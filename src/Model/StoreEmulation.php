<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Store\Model\App\Emulation;

class StoreEmulation
{
    /** @var Emulation */
    private $emulation;
    /**
     * @var AreaList
     */
    private $areaList;

    public function __construct(Emulation $emulation, AreaList $areaList)
    {
        $this->emulation = $emulation;
        $this->areaList = $areaList;
    }

    public function runInStore(int $storeId, callable $proceed)
    {
        try {
            $this->emulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);

            $area = $this->areaList->getArea(\Magento\Framework\App\Area::AREA_FRONTEND);
            $area->load(\Magento\Framework\App\Area::PART_TRANSLATE);

            $proceed();
        } finally {
            $this->emulation->stopEnvironmentEmulation();
        }
    }
}
