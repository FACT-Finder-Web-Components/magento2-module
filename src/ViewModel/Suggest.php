<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Omikron\Factfinder\Model\Config\CmsConfig;

class Suggest implements ArgumentInterface
{
    /** @var CmsConfig */
    private $cmsConfig;

    public function __construct(CmsConfig $cmsConfig)
    {
        $this->cmsConfig = $cmsConfig;
    }

    public function isCmsEnabled(): bool
    {
        return $this->cmsConfig->isExportEnabled();
    }

    public function useSeparateChannelForCms(): bool
    {
        return $this->cmsConfig->useSeparateChannel();
    }
}
