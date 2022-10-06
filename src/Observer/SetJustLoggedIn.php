<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadata;

class SetJustLoggedIn extends CookieModifierObserver implements ObserverInterface
{
    /**
     * @param Observer $_
     *
     * @SuppressWarnings(PHPMD)
     */
    public function execute(Observer $_): void
    {
        $cookieMetadata = $this->createCookieMetadata();
        $this->cookieManager->setPublicCookie(self::HAS_JUST_LOGGED_IN_COOKIE_NAME, 1, $cookieMetadata);
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
