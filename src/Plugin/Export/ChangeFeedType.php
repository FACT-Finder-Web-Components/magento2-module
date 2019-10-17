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
     * @param mixed  $subject
     * @param string $type
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeCreate($subject, string $type): array
    {
        if ($type == 'product' && $this->config->isExportEnabled() && !$this->config->useSeparateChannel()) {
            $type = 'combined';
        }
        return [$type];
    }
}
