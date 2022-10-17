<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Model\SessionData;

class AnonymizeUserId
{
    private const PATH_ANONYMIZE_USER_ID = 'factfinder/advanced/anonymize_user_id';

    public function __construct(private readonly ScopeConfigInterface $scopeConfig)
    {}

    /**
     * @param SessionData $_
     * @param int         $userId
     *
     * @return string|int
     * @SuppressWarnings(PHPMD)
     */
    public function afterGetUserId(SessionData $_, string $userId)
    {
        return $this->isAnonymizationEnabled() && $userId ? md5($userId) : $userId; // phpcs:ignore
    }

    private function isAnonymizationEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::PATH_ANONYMIZE_USER_ID, ScopeInterface::SCOPE_STORES);
    }
}
