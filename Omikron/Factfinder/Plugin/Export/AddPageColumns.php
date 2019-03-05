<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Plugin\Export;

use Omikron\Factfinder\Api\Export\StreamInterface;
use Omikron\Factfinder\Api\ExporterInterface;
use Omikron\Factfinder\Model\Config\CmsConfig;

class AddPageColumns
{
    /** @var CmsConfig */
    private $cmsConfig;

    /** @var array */
    private $columns;

    public function __construct(CmsConfig $cmsConfig, array $columns)
    {
        $this->cmsConfig = $cmsConfig;
        $this->columns   = $columns;
    }

    public function beforeExportEntities(
        ExporterInterface $subject,
        StreamInterface $stream,
        array $dataProviders,
        array $columns = []
    ) {
        if ($this->cmsConfig->isCmsExportEnabled() && !$this->cmsConfig->useSeparateCmsChannel()) {
            $columns = array_merge($columns, $this->columns);
        }

        return [$stream, $dataProviders, $columns];
    }
}
