<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export;

use Magento\Framework\ObjectManagerInterface;
use Omikron\Factfinder\Model\Config\CmsConfig;
use Omikron\Factfinder\Model\Export\Catalog\DataProvider as ProductDataProvider;
use Omikron\Factfinder\Model\Export\Cms\DataProvider as CmsDataProvider;

class DataProvidersFactory
{
    /** @var ObjectManagerInterface */
    private $objectManager;

    /** @var CmsConfig */
    private $cmsConfig;

    public function __construct(ObjectManagerInterface $objectManager, CmsConfig $cmsConfig)
    {
        $this->objectManager = $objectManager;
        $this->cmsConfig     = $cmsConfig;
    }

    public function create(): array
    {
        $providers = [$this->objectManager->create(ProductDataProvider::class)];
        if ($this->cmsConfig->isCmsExportEnabled() && !$this->cmsConfig->useSeparateCmsChannel()) {
            $providers[] = $this->objectManager->create(CmsDataProvider::class);
        }
        return $providers;
    }
}
