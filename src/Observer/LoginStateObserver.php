<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

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
        SessionConfig $sessionConfig
    ) {
        $this->sessionData = $sessionData;
        $this->sessionConfig = $sessionConfig;
    }

    protected function setCookie(string $name, string $value): void
    {
        setcookie(
            $name,
            $value,
            (new \DateTime())->modify('+1 hour')->getTimestamp(),
            '/'
        );
    }

    protected function clearCookie(string $name): void
    {
        unset($_COOKIE[$name]);
        setcookie($name, '', -1, '/');
    }
}
