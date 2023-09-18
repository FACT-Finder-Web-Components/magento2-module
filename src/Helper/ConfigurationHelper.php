<?php
declare(strict_types=1);

namespace Omikron\Factfinder\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class ConfigurationHelper extends AbstractHelper
{
    public function getConfig(string $configPath)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            ScopeInterface::SCOPE_STORE
        );
    }
}
