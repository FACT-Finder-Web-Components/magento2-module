<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Plugin\Export\Cms;

use Omikron\Factfinder\Model\Config\CmsConfig;
use Omikron\Factfinder\Model\Export\Cms\Page;

class FillMainArticleNumber
{
    /** @var CmsConfig */
    private $cmsConfig;

    public function __construct(CmsConfig $cmsConfig)
    {
        $this->cmsConfig = $cmsConfig;
    }

    public function afterToArray(Page $subject, array $entityData)
    {
        if ($this->cmsConfig->isCmsExportEnabled() && !$this->cmsConfig->useSeparateCmsChannel()) {
            $mainArticleNumber = $this->cmsConfig->getMainProductArticle();
            $entityData[$mainArticleNumber] = $entityData['PageId'];
        }
        return $entityData;
    }
}
