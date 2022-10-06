<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Framework\Session\Config\ConfigInterface as SessionConfig;
use Magento\Framework\Stdlib\Cookie\CookieMetadata;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Omikron\Factfinder\Model\SessionData;

abstract class CookieModifierObserver
{
    protected const HAS_JUST_LOGGED_IN_COOKIE_NAME = 'has_just_logged_in';
    protected const USER_ID_COOKIE_NAME = 'user_id';

    protected CookieManagerInterface $cookieManager;
    protected CookieMetadataFactory $cookieMetadataFactory;
    protected SessionConfig $sessionConfig;
    protected SessionData $sessionData;

    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory  $cookieMetadataFactory,
        SessionConfig          $sessionConfig,
        SessionData            $sessionData
    ) {
        $this->cookieManager         = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionConfig         = $sessionConfig;
        $this->sessionData           = $sessionData;
    }

    protected function createCookieMetadata(): CookieMetadata
    {
        return $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration(3600)
            ->setPath($this->sessionConfig->getCookiePath())
            ->setDomain($this->sessionConfig->getCookieDomain())
            ->setSecure($this->sessionConfig->getCookieSecure())
            ->setHttpOnly(false);
    }
}
