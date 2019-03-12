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

    /**
     * @param mixed  $_
     * @param string $type
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeCreate($_, string $type): array
    {
        if ($type == 'product' && $this->config->isExportEnabled() && !$this->config->useSeparateChannel()) {
            $type = 'combined';
        }
        return [$type];
    }
}
