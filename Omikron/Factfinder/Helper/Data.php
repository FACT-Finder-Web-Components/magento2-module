<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * @todo remove this class
 */
class Data extends AbstractHelper
{
    public const FRONT_NAME                        = 'FACT-Finder';
    public const EXPORT_PAGE                       = 'export';
    public const CUSTOM_RESULT_PAGE                = 'result';
    private const PATH_DATA_TRANSFER_IMPORT         = 'factfinder/data_transfer/ff_push_import_enabled';
    private const PATH_CONFIGURABLE_CRON_IS_ENABLED = 'factfinder/configurable_cron/ff_cron_enabled';

    public function isPushImportEnabled($scopeCode = null) : bool
    {
        return $this->scopeConfig->isSetFlag(self::PATH_DATA_TRANSFER_IMPORT, 'store', $scopeCode);
    }

    public function isCronEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::PATH_CONFIGURABLE_CRON_IS_ENABLED);
    }
}
