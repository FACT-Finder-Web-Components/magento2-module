<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use DateTime;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Session\Config\ConfigInterface as SessionConfig;
use Omikron\Factfinder\Model\SessionData;

abstract class LoginStateObserver implements ObserverInterface
{
    public const HAS_JUST_LOGGED_IN = 'ff_has_just_logged_in';
    public const HAS_JUST_LOGGED_OUT = 'ff_has_just_logged_out';
    public const USER_ID = 'ff_user_id';

    protected SessionData $sessionData;
    protected SessionConfig $sessionConfig;

    public function __construct(
        SessionData $sessionData,
        SessionConfig $sessionConfig,
    ) {
        $this->sessionData = $sessionData;
        $this->sessionConfig = $sessionConfig;
    }

    /**
     * phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
     */
    protected function setCookie(string $name, string $value): void
    {
        setcookie(
            $name,
            $value,
            (new DateTime())->modify('+1 hour')->getTimestamp(),
            '/'
        );
    }

    /**
     * @SuppressWarnings(PHPMD)
     * phpcs:disable Magento2.Security.Superglobal.SuperglobalUsageWarning
     * phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
     */
    protected function clearCookie(string $name): void
    {
        unset($_COOKIE[$name]);
        setcookie($name, '', -1, '/');
    }
}
