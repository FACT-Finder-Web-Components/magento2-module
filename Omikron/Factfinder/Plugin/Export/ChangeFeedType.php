<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Plugin\Export;

use Omikron\Factfinder\Model\Config\CmsConfig;
use Omikron\Factfinder\Model\Export\FeedFactory;

class ChangeFeedType
{
    /** @var CmsConfig  */
    private $cmsConfig;

    public function __construct(CmsConfig $cmsConfig)
    {
        $this->cmsConfig = $cmsConfig;
    }

    public function beforeCreate(FeedFactory $subject, string $type)
    {
        if ($type == 'product' && $this->cmsConfig->isCmsExportEnabled() && !$this->cmsConfig->useSeparateCmsChannel()) {
            $type = 'combined';
        }
        return [$type];
    }
}
