<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RemoveUserCookie extends UserCookie implements ObserverInterface
{
    /**
     * @param Observer $_
     *
     * @SuppressWarnings(PHPMD)
     */
    public function execute(Observer $observer)
    {
        $this->cookieManager->deleteCookie(self::USER_ID_COOKIE_NAME);
    }
}
