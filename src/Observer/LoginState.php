<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Framework\Event\Observer;

class LoginState extends LoginStateObserver
{
    /**
     * @param Observer $_
     *
     * @SuppressWarnings(PHPMD)
     */
    public function execute(Observer $observer)
    {
        if ($this->sessionData->getUserId() === '') {
            $this->clearCookie(self::USER_ID);

            return;
        }

        $this->setCookie(self::USER_ID, $this->sessionData->getUserId());
    }
}
