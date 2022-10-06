<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CheckLoginState extends CookieModifierObserver implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        if ($this->sessionData->getUserId()) {
            $this->cookieManager->setPublicCookie(self::USER_ID_COOKIE_NAME, $this->sessionData->getUserId(), $this->createCookieMetadata());
        } else {
            $this->cookieManager->deleteCookie(self::USER_ID_COOKIE_NAME);
        }
    }
}
