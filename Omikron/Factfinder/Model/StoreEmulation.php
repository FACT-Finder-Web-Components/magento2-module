<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Framework\App\Area;
use Magento\Store\Model\App\Emulation;

class StoreEmulation
{
    /** @var Emulation */
    private $emulation;

    public function __construct(Emulation $emulation)
    {
        $this->emulation = $emulation;
    }

    public function runInStore(string $storeId, callable $proceed)
    {
        try {
            $this->emulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);
            $proceed();
        } finally {
            $this->emulation->stopEnvironmentEmulation();
        }
    }
}
