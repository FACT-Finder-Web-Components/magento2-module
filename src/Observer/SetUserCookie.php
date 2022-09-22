<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Session\Config\ConfigInterface as SessionConfig;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

class SetUserCookie extends UserCookie implements ObserverInterface
{
    /**
     * @param Observer $_
     *
     * @SuppressWarnings(PHPMD)
     */
    public function execute(Observer $_): void
    {
        $cookieMetadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration(3600)
            ->setPath($this->sessionConfig->getCookiePath())
            ->setDomain($this->sessionConfig->getCookieDomain())
            ->setSecure($this->sessionConfig->getCookieSecure())
            ->setHttpOnly(false);

        $this->cookieManager->setPublicCookie(self::USER_ID_COOKIE_NAME,  $this->sessionData->getUserId(), $cookieMetadata);
        $this->cookieManager->setPublicCookie(self::HAS_JUST_LOGGED_IN_COOKIE_NAME, 1, $cookieMetadata);
    }
}
