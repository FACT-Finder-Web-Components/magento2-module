<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Plugin\Export;

use Omikron\Factfinder\Model\Config\CmsConfig;

class ChangeFeedType
{
    /** @var CmsConfig */
    private $config;

    public function __construct(CmsConfig $config)
    {
        $this->config = $config;
    }

    public function beforeCreate($_, string $type): array
    {
        if ($type == 'product' && $this->config->isCmsExportEnabled() && !$this->config->useSeparateCmsChannel()) {
            $type = 'combined';
        }
        return [$type];
    }
}
